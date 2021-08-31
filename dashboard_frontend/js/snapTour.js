(function (win) {
    class Storage {
        constructor(reset, storageId) {
            this._storageId = storageId;

            const retrieved = win.localStorage.getItem(this._storageId);
            if (retrieved && !reset) {
                this._inner = JSON.parse(retrieved);
            } else {
                this.reset();
            }

            return new Proxy(this, {
                get(obj, prop) {
                    if (prop in obj)
                        return typeof (obj[prop]) === 'function' ? obj[prop].bind(obj) : obj[prop];
                    return obj._getItem(prop);
                },
                set(obj, prop, value) {
                    obj._setItem(prop, value);
                    return true;
                }
            })
        }

        _getItem(name) {
            return this._inner[name];
        }

        _setItem(name, value) {
            const previous = this._inner[name];
            if (previous !== value) {
                this._inner[name] = value;
                win.localStorage.setItem(this._storageId, JSON.stringify(this._inner));
            }
        }

        reset() {
            this._inner = {};
            win.localStorage.setItem(this._storageId, JSON.stringify(this._inner));
        }
    }

    class SnapTourManager {
        static openModeToTargetMap = {
            iframe: "_self",
            samePage: "_self",
            newTab: "_blank"
        }

        constructor(tourId, options) {
            this._options = Object.assign({}, {
                isPublic: true,
              //  resetTimeout: 1000 * 60 * 60 * 24
                resetTimeout: 1000 * 60 * 5
            }, options);

            this._initStorage(tourId);
            this._initTour();
            this._steps = [];
        }

        get currentStepIdx() {
            return this._storage.currentStepIdx || 0;
        }

        set currentStepIdx(value) {
            this._storage.currentStepIdx = value;
        }

        get resetTimeoutExpired() {
            return Date.now() - this._storage.tourStartTime >= this._options.resetTimeout;
        }

        _initStorage(tourId) {
            const params = new URLSearchParams(win.location.search);
            this._storage = new Storage(params.has("resetTour"), `SnapTour:${tourId}`);

            if (this._options.isPublic && this.resetTimeoutExpired)
                this._storage.reset();
        }

        _initTour() {
            const tour = new Shepherd.Tour({
                defaultStepOptions: {
                    cancelIcon: {
                        enabled: true
                    },
                    scrollTo: { behavior: 'smooth', block: 'center' },
                },
                useModalOverlay: true
            });

            tour.on("complete", () => this._storage.completed = true);
            tour.on("cancel", () => this._storage.cancelled = true);

            this._tour = tour;
        }

        addStep(step) {
            this._steps.push(step);
            this._tour.addStep(this._stepOptions(step));
            return this;
        }

        start() {
            if (!this._storage.completed && !this._storage.cancelled)
                this._tour.show(this.currentStepIdx || 0);

            if (this.currentStepIdx == 0)
                this._storage.tourStartTime = Date.now();
        }

        _nextBtnText(nullableText) {
            return nullableText || '<i class="fa fa-chevron-right" aria-hidden="true"></i>';
        }

        _previousBtnText(nullableText) {
            return nullableText || '<i class="fa fa-chevron-left" aria-hidden="true"></i>';
        }

        _btnOptions(step) {
            const self = this;

            let buttons = [];
            if (step.withPreviousStepBtn) {
                buttons.push({
                    action() {
                        self._goToPrevious();
                    },
                    classes: "shepherd-prev-btn",
                    secondary: true,
                    text: this._previousBtnText(step.previousStepBtnText)
                });
            }

            if (step.withCancelBtn) {
                buttons.push({
                    action() {
                        self._tour.cancel();
                    },
                    secondary: true,
                    text: step.cancelBtnText || "Leave the tour"
                });
            }

            if (step.isLastStep) {
                if (step.urlToOpenOnNext) {
                    buttons.push({
                        action() {
                            this.complete();
                        },
                        secondary: true,
                        text: "Close"
                    });
                    buttons.push({
                        action() {
                            this.complete();
                            self._openNextStepUrl(step);
                        },
                        text: this._nextBtnText(step.nextBtnText)
                    });
                } else {
                    buttons.push({
                        action() {
                            this.complete();
                        },
                        text: this._nextBtnText(step.nextBtnText)
                    });
                }
            } else {
                buttons.push({
                    action() {
                        self._goToNext(step);
                    },
                    text: this._nextBtnText(step.nextBtnText)
                });
            }

            return buttons;
        }

        _stepOptions(step) {
            const { stepId, title, text, attachToElementSelector, menuToOpenOnShow } = step;
            let opt = {
                id: stepId,
                title,
                text,
                buttons: this._btnOptions(step)
            };

            if (attachToElementSelector) {
                opt.attachTo = {
                    element: attachToElementSelector,
                    on: "auto"
                };
                opt.beforeShowPromise = () => {
                    if (this._getDOMEl(attachToElementSelector))
                        return Promise.resolve();

                    return new Promise((resolve, reject) => {
                        let counter = 0;
                        const timer = setInterval(() => {
                            if (this._getDOMEl(attachToElementSelector)) {
                                clearInterval(timer);
                                resolve();
                            } else {
                                counter++;
                            }

                            if (counter == 20) {
                                clearInterval(timer);
                                reject;
                            }
                        }, 50);
                    });
                };
            }

            if (menuToOpenOnShow) {
                opt.when = {
                    show: () => {
                        this._getDOMEl(menuToOpenOnShow).click();
                    }
                };
            }

            return opt;
        }

        _getDOMEl(selector) {
            return document.querySelector(selector);
        }

        _goToPrevious() {
            const prevStep = this._steps[this.currentStepIdx - 1];
            if (prevStep) {
                this.currentStepIdx--;

                if (prevStep.urlToOpenOnNext) {
                    win.history.back();
                } else {
                    this._tour.back();
                }
            }
        }

        _goToNext(currentStep) {
            this.currentStepIdx++;
            if (currentStep.urlToOpenOnNext) {
                this._openNextStepUrl(currentStep);
            } else {
                this._tour.next();
            }
        }

        _openNextStepUrl(step) {
            win.open(step.urlToOpenOnNext, SnapTourManager.openModeToTargetMap[step.urlOpenMode]);
        }
    }

    function* iterateSteps(steps) {
        let current = steps.find(x => x.isFirstStep);
        yield current;
        while (current.nextStepId) {
            current = steps.find(x => x.stepId === current.nextStepId);
            yield current;
        }
    }

    win.SnapTour = {
        init(steps, options) {
            if (!steps && steps.lenght > 0)
                throw new Error("Initialized SnapTour without steps");

            const tourId = steps[0].tourName;
            const tour = new SnapTourManager(tourId, options);
            for (const step of iterateSteps(steps)) {
                tour.addStep(step);
            }
            tour.start();
        }
    };
})(window);

