(function (root, factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.WidgetMapExport = factory();
    }
}(typeof self !== 'undefined' ? self : this, function () {
    'use strict';

    var CAPTURE_HIDDEN_SELECTORS = [
        '.leaflet-control-zoom',
        '.leaflet-control-zoom-display',
        '.mapOptions',
        '.mapOptionsRight',
        '.widget-map-export-control'
    ];

    var STYLE_KEYS = [
        'color',
        'dashArray',
        'fillColor',
        'fillOpacity',
        'opacity',
        'radius',
        'weight'
    ];

    function clone(value) {
        return JSON.parse(JSON.stringify(value));
    }

    function normalizeBounds(bounds) {
        if (!bounds) {
            return null;
        }
        if (typeof bounds.getWest === 'function') {
            return {
                west: bounds.getWest(),
                south: bounds.getSouth(),
                east: bounds.getEast(),
                north: bounds.getNorth()
            };
        }
        if (bounds._southWest && bounds._northEast) {
            return {
                west: bounds._southWest.lng,
                south: bounds._southWest.lat,
                east: bounds._northEast.lng,
                north: bounds._northEast.lat
            };
        }
        return {
            west: bounds.west,
            south: bounds.south,
            east: bounds.east,
            north: bounds.north
        };
    }

    function viewportRectangles(bounds) {
        if (bounds.west <= bounds.east) {
            return [bounds];
        }
        return [
            {west: bounds.west, south: bounds.south, east: 180, north: bounds.north},
            {west: -180, south: bounds.south, east: bounds.east, north: bounds.north}
        ];
    }

    function pointInRectangle(point, rectangle) {
        return point[0] >= rectangle.west && point[0] <= rectangle.east &&
            point[1] >= rectangle.south && point[1] <= rectangle.north;
    }

    function segmentIntersectsRectangle(start, end, rectangle) {
        if (pointInRectangle(start, rectangle) || pointInRectangle(end, rectangle)) {
            return true;
        }

        var deltaX = end[0] - start[0];
        var deltaY = end[1] - start[1];
        var p = [-deltaX, deltaX, -deltaY, deltaY];
        var q = [
            start[0] - rectangle.west,
            rectangle.east - start[0],
            start[1] - rectangle.south,
            rectangle.north - start[1]
        ];
        var minimum = 0;
        var maximum = 1;

        for (var index = 0; index < 4; index++) {
            if (p[index] === 0) {
                if (q[index] < 0) {
                    return false;
                }
                continue;
            }
            var ratio = q[index] / p[index];
            if (p[index] < 0) {
                minimum = Math.max(minimum, ratio);
            } else {
                maximum = Math.min(maximum, ratio);
            }
            if (minimum > maximum) {
                return false;
            }
        }
        return true;
    }

    function lineIntersectsRectangle(coordinates, rectangle) {
        if (!Array.isArray(coordinates) || coordinates.length === 0) {
            return false;
        }
        if (coordinates.some(function (point) { return pointInRectangle(point, rectangle); })) {
            return true;
        }
        for (var index = 1; index < coordinates.length; index++) {
            if (segmentIntersectsRectangle(coordinates[index - 1], coordinates[index], rectangle)) {
                return true;
            }
        }
        return false;
    }

    function pointInRing(point, ring) {
        var inside = false;
        for (var current = 0, previous = ring.length - 1; current < ring.length; previous = current++) {
            var currentPoint = ring[current];
            var previousPoint = ring[previous];
            var crossesLatitude = (currentPoint[1] > point[1]) !== (previousPoint[1] > point[1]);
            if (crossesLatitude &&
                point[0] < (previousPoint[0] - currentPoint[0]) *
                    (point[1] - currentPoint[1]) /
                    (previousPoint[1] - currentPoint[1]) + currentPoint[0]) {
                inside = !inside;
            }
        }
        return inside;
    }

    function polygonContainsPoint(rings, point) {
        if (!rings.length || !pointInRing(point, rings[0])) {
            return false;
        }
        return !rings.slice(1).some(function (hole) {
            return pointInRing(point, hole);
        });
    }

    function polygonIntersectsRectangle(rings, rectangle) {
        if (!rings || !rings.length) {
            return false;
        }
        if (rings.some(function (ring) { return lineIntersectsRectangle(ring, rectangle); })) {
            return true;
        }
        var corners = [
            [rectangle.west, rectangle.south],
            [rectangle.west, rectangle.north],
            [rectangle.east, rectangle.south],
            [rectangle.east, rectangle.north]
        ];
        return corners.some(function (corner) {
            return polygonContainsPoint(rings, corner);
        });
    }

    function geometryIntersectsRectangle(geometry, rectangle) {
        var coordinates = geometry.coordinates || [];
        switch (geometry.type) {
        case 'Point':
            return pointInRectangle(coordinates, rectangle);
        case 'MultiPoint':
            return coordinates.some(function (point) { return pointInRectangle(point, rectangle); });
        case 'LineString':
            return lineIntersectsRectangle(coordinates, rectangle);
        case 'MultiLineString':
            return coordinates.some(function (line) { return lineIntersectsRectangle(line, rectangle); });
        case 'Polygon':
            return polygonIntersectsRectangle(coordinates, rectangle);
        case 'MultiPolygon':
            return coordinates.some(function (polygon) {
                return polygonIntersectsRectangle(polygon, rectangle);
            });
        case 'GeometryCollection':
            return (geometry.geometries || []).some(function (child) {
                return geometryIntersectsRectangle(child, rectangle);
            });
        default:
            return false;
        }
    }

    function geometryTouchesBounds(geometry, bounds) {
        if (!bounds) {
            return true;
        }
        if (!geometry) {
            return false;
        }
        return viewportRectangles(bounds).some(function (rectangle) {
            return geometryIntersectsRectangle(geometry, rectangle);
        });
    }

    function rectanglesOverlap(first, second) {
        return first.east >= second.west && first.west <= second.east &&
            first.north >= second.south && first.south <= second.north;
    }

    function layerTouchesBounds(layer, geometry, bounds) {
        if (geometry && geometry.type === 'Point' &&
            layer && typeof layer.getRadius === 'function' &&
            typeof layer.getBounds === 'function') {
            var layerBounds = normalizeBounds(layer.getBounds());
            if (layerBounds) {
                return viewportRectangles(bounds).some(function (viewportRectangle) {
                    return viewportRectangles(layerBounds).some(function (layerRectangle) {
                        return rectanglesOverlap(viewportRectangle, layerRectangle);
                    });
                });
            }
        }
        return geometryTouchesBounds(geometry, bounds);
    }

    function layerStyle(layer) {
        var style = {};
        var options = layer && layer.options ? layer.options : {};
        STYLE_KEYS.forEach(function (key) {
            if (options[key] !== undefined && options[key] !== null) {
                style[key] = options[key];
            }
        });
        if (layer && typeof layer.getRadius === 'function') {
            style.radius = layer.getRadius();
        }
        return style;
    }

    function layerSource(layer) {
        if (layer && typeof layer._url === 'string') {
            return layer._url;
        }
        if (layer && layer.constructor && layer.constructor.name) {
            return layer.constructor.name;
        }
        return 'unknown';
    }

    function excludedLayer(layer) {
        if (!layer) {
            return null;
        }
        var details = {};
        if (layer._leaflet_id !== undefined && layer._leaflet_id !== null) {
            details.layerId = layer._leaflet_id;
        }
        if (layer.options) {
            if (layer.options.layers !== undefined) {
                details.layers = layer.options.layers;
            }
            if (layer.options.name !== undefined) {
                details.name = layer.options.name;
            } else if (layer.options.title !== undefined) {
                details.name = layer.options.title;
            }
        }
        if (typeof layer.getTileUrl === 'function') {
            return Object.assign({kind: 'raster-tile', source: layerSource(layer)}, details);
        }
        if (layer._image || layer._heatmap || layer._canvas) {
            return Object.assign({kind: 'raster-overlay', source: layerSource(layer)}, details);
        }
        return null;
    }

    function flattenGeoJson(geoJson) {
        if (!geoJson) {
            return [];
        }
        if (geoJson.type === 'FeatureCollection') {
            return geoJson.features || [];
        }
        if (geoJson.type === 'Feature') {
            return [geoJson];
        }
        if (geoJson.type && geoJson.coordinates) {
            return [{type: 'Feature', properties: {}, geometry: geoJson}];
        }
        return [];
    }

    function createVisibleFeatureCollection(map, options) {
        options = options || {};
        var bounds = normalizeBounds(options.bounds || map.getBounds());
        var features = [];
        var excludedLayers = [];
        var excludedSignatures = {};
        var visitedLayers = [];

        function addExcluded(item) {
            var signature = JSON.stringify(item);
            if (!excludedSignatures[signature]) {
                excludedSignatures[signature] = true;
                excludedLayers.push(item);
            }
        }

        function visit(layer) {
            if (!layer || visitedLayers.indexOf(layer) !== -1) {
                return;
            }
            visitedLayers.push(layer);

            var excluded = excludedLayer(layer);
            if (excluded) {
                addExcluded(excluded);
                return;
            }

            if (typeof layer.eachLayer === 'function') {
                layer.eachLayer(visit);
                return;
            }

            if (typeof layer.toGeoJSON === 'function') {
                var style = layerStyle(layer);
                var hasStyle = Object.keys(style).length > 0;
                flattenGeoJson(layer.toGeoJSON()).forEach(function (sourceFeature) {
                    if (!sourceFeature.geometry || !layerTouchesBounds(layer, sourceFeature.geometry, bounds)) {
                        return;
                    }
                    var feature = clone(sourceFeature);
                    feature.properties = feature.properties || {};
                    if (hasStyle) {
                        feature.properties._mapStyle = style;
                    }
                    features.push(feature);
                });
                return;
            }

            addExcluded({kind: 'non-vector', source: layerSource(layer)});
        }

        map.eachLayer(visit);
        (options.additionalExcludedLayers || []).forEach(addExcluded);

        return {
            type: 'FeatureCollection',
            features: features,
            metadata: {
                exportedAt: options.exportedAt || new Date().toISOString(),
                bounds: bounds,
                zoom: typeof map.getZoom === 'function' ? map.getZoom() : null,
                excludedLayers: excludedLayers
            }
        };
    }

    function calculateControlRightOffset(panelState, additionalWidths) {
        return 8;
    }

    function setExportBusyState(control, toggle, busy, statusDelayMs, busyMessage) {
        if (!control || !toggle) {
            return;
        }

        if (control._widgetMapExportStatusTimer) {
            clearTimeout(control._widgetMapExportStatusTimer);
            control._widgetMapExportStatusTimer = null;
        }

        var icon = toggle.querySelector('i');
        var accessibleLabel = toggle.querySelector('.sr-only');
        var status = control.querySelector('.widget-map-export-status');
        var isBusy = busy === true;
        var message = busyMessage || 'Preparing image...';
        var accessibleMessage = message.replace(/\.\.\.$/, '');

        control.classList.toggle('busy', isBusy);
        control.classList.remove('show-status');
        toggle.disabled = isBusy;
        toggle.setAttribute('aria-busy', isBusy ? 'true' : 'false');
        toggle.title = isBusy ? message : 'Export map';

        if (accessibleLabel) {
            accessibleLabel.textContent = isBusy ? accessibleMessage : 'Export map';
        }
        if (icon) {
            icon.classList.toggle('fa-bars', !isBusy);
            icon.classList.toggle('fa-spinner', isBusy);
            icon.classList.toggle('fa-spin', isBusy);
        }
        if (status) {
            status.textContent = '';
        }

        if (isBusy && status) {
            var delay = typeof statusDelayMs === 'number' ? statusDelayMs : 800;
            var showStatus = function () {
                control._widgetMapExportStatusTimer = null;
                if (control.classList.contains('busy')) {
                    status.textContent = message;
                    control.classList.add('show-status');
                }
            };
            if (delay <= 0) {
                showStatus();
            } else {
                control._widgetMapExportStatusTimer = setTimeout(showStatus, delay);
            }
        }
    }

    function findCorsImageSamples(images, pageOrigin) {
        var samplesByOrigin = {};
        (images || []).forEach(function (image) {
            var src = image.currentSrc || image.src;
            if (!src || src.indexOf('data:') === 0 || src.indexOf('blob:') === 0) {
                return;
            }
            var parsed;
            try {
                parsed = new URL(src, pageOrigin);
            } catch (error) {
                return;
            }
            if (parsed.origin === pageOrigin || image.crossOrigin) {
                return;
            }
            if (!samplesByOrigin[parsed.origin]) {
                samplesByOrigin[parsed.origin] = parsed.href;
            }
        });
        return Object.keys(samplesByOrigin).map(function (origin) {
            return samplesByOrigin[origin];
        });
    }

    function testCorsImage(url, timeoutMs, ImageConstructor) {
        return new Promise(function (resolve) {
            var image = new (ImageConstructor || Image)();
            var completed = false;
            var timer;
            var complete = function (result) {
                if (completed) {
                    return;
                }
                completed = true;
                clearTimeout(timer);
                resolve(result);
            };
            image.crossOrigin = 'anonymous';
            image.onload = function () {
                complete(null);
            };
            image.onerror = function () {
                complete(url);
            };
            timer = setTimeout(function () {
                complete(url);
            }, timeoutMs === undefined ? 4000 : timeoutMs);
            image.src = url;
        });
    }

    function findCorsFailures(rootElement) {
        if (typeof window === 'undefined') {
            return Promise.resolve([]);
        }
        var samples = findCorsImageSamples(
            Array.prototype.slice.call(rootElement.querySelectorAll('img')),
            window.location.origin
        );
        return Promise.all(samples.map(function (url) {
            return testCorsImage(url);
        })).then(function (results) {
            return results.filter(function (url) {
                return url !== null;
            });
        });
    }

    function matchesSelector(element, selector) {
        if (!element) {
            return false;
        }
        var matcher = element.matches || element.msMatchesSelector || element.webkitMatchesSelector;
        return typeof matcher === 'function' ? matcher.call(element, selector) : false;
    }

    function isControlExcludedFromCapture(element) {
        return CAPTURE_HIDDEN_SELECTORS.some(function (selector) {
            return matchesSelector(element, selector);
        });
    }

    function prepareLeafletMarkersForCapture(rootElement) {
        var markerSelector = '.leaflet-marker-icon, .leaflet-marker-shadow';
        var markers = Array.prototype.slice.call(rootElement.querySelectorAll(markerSelector));
        var noOpCapture = {
            paint: function () {},
            restore: function () {},
            shouldIgnore: function () { return false; }
        };
        if (!markers.length || typeof rootElement.getBoundingClientRect !== 'function') {
            return noOpCapture;
        }

        var rootRect = rootElement.getBoundingClientRect();
        var ignoredMarkers = [];
        var drawableMarkers = [];
        var occluderSelector = '.leaflet-control, #universal-top-right, .leaflet-popup, .leaflet-tooltip';
        var occluderRectangles = Array.prototype.slice.call(
            rootElement.querySelectorAll(occluderSelector)
        ).reduce(function (rectangles, element) {
            if (!element || typeof element.getBoundingClientRect !== 'function') {
                return rectangles;
            }
            var style = typeof window.getComputedStyle === 'function' ?
                window.getComputedStyle(element) : element.style;
            if (style.display === 'none' || style.visibility === 'hidden' ||
                parseFloat(style.opacity) === 0) {
                return rectangles;
            }
            var rectangle = element.getBoundingClientRect();
            if (rectangle.width > 0 && rectangle.height > 0) {
                if (isControlExcludedFromCapture(element)) {
                    return rectangles;
                }
                rectangles.push({
                    left: rectangle.left,
                    top: rectangle.top,
                    right: rectangle.right === undefined ? rectangle.left + rectangle.width : rectangle.right,
                    bottom: rectangle.bottom === undefined ? rectangle.top + rectangle.height : rectangle.bottom,
                    width: rectangle.width,
                    height: rectangle.height
                });
            }
            return rectangles;
        }, []);

        function rectanglesOverlap(first, second) {
            var firstRight = first.right === undefined ? first.left + first.width : first.right;
            var firstBottom = first.bottom === undefined ? first.top + first.height : first.bottom;
            var secondRight = second.right === undefined ? second.left + second.width : second.right;
            var secondBottom = second.bottom === undefined ? second.top + second.height : second.bottom;
            return first.left < secondRight && firstRight > second.left &&
                first.top < secondBottom && firstBottom > second.top;
        }

        function subtractRectangle(region, occluder) {
            if (!rectanglesOverlap(region, occluder)) {
                return [region];
            }

            var overlapLeft = Math.max(region.left, occluder.left);
            var overlapTop = Math.max(region.top, occluder.top);
            var overlapRight = Math.min(region.right, occluder.right);
            var overlapBottom = Math.min(region.bottom, occluder.bottom);
            var remaining = [];

            if (region.top < overlapTop) {
                remaining.push({
                    left: region.left,
                    top: region.top,
                    right: region.right,
                    bottom: overlapTop
                });
            }
            if (overlapBottom < region.bottom) {
                remaining.push({
                    left: region.left,
                    top: overlapBottom,
                    right: region.right,
                    bottom: region.bottom
                });
            }
            if (region.left < overlapLeft) {
                remaining.push({
                    left: region.left,
                    top: overlapTop,
                    right: overlapLeft,
                    bottom: overlapBottom
                });
            }
            if (overlapRight < region.right) {
                remaining.push({
                    left: overlapRight,
                    top: overlapTop,
                    right: region.right,
                    bottom: overlapBottom
                });
            }
            return remaining;
        }

        function isCanvasSafeImage(marker) {
            if (!marker.tagName || marker.tagName.toLowerCase() !== 'img' ||
                marker.complete === false || marker.naturalWidth === 0) {
                return false;
            }
            var source = marker.currentSrc || marker.src;
            if (!source || source.indexOf('data:') === 0 || source.indexOf('blob:') === 0) {
                return true;
            }
            try {
                return new URL(source, window.location.origin).origin === window.location.origin ||
                    Boolean(marker.crossOrigin);
            } catch (error) {
                return false;
            }
        }

        markers.forEach(function (marker) {
            if (!marker || !isCanvasSafeImage(marker) || typeof marker.getBoundingClientRect !== 'function') {
                return;
            }
            var computedStyle = typeof window.getComputedStyle === 'function' ?
                window.getComputedStyle(marker) : marker.style;
            if (computedStyle.display === 'none' || computedStyle.visibility === 'hidden' ||
                parseFloat(computedStyle.opacity) === 0) {
                return;
            }

            var markerRect = marker.getBoundingClientRect();
            var markerRight = markerRect.right === undefined ? markerRect.left + markerRect.width : markerRect.right;
            var markerBottom = markerRect.bottom === undefined ? markerRect.top + markerRect.height : markerRect.bottom;
            var rootRight = rootRect.right === undefined ? rootRect.left + rootRect.width : rootRect.right;
            var rootBottom = rootRect.bottom === undefined ? rootRect.top + rootRect.height : rootRect.bottom;
            if (markerRect.width <= 0 || markerRect.height <= 0 ||
                markerRight <= rootRect.left || markerRect.left >= rootRight ||
                markerBottom <= rootRect.top || markerRect.top >= rootBottom) {
                return;
            }

            ignoredMarkers.push(marker);
            var visibleRegions = [{
                left: markerRect.left,
                top: markerRect.top,
                right: markerRight,
                bottom: markerBottom
            }];
            occluderRectangles.forEach(function (occluder) {
                visibleRegions = visibleRegions.reduce(function (regions, region) {
                    return regions.concat(subtractRectangle(region, occluder));
                }, []);
            });
            if (!visibleRegions.length) {
                return;
            }

            var parentStyle = marker.parentElement && typeof window.getComputedStyle === 'function' ?
                window.getComputedStyle(marker.parentElement) : null;
            drawableMarkers.push({
                element: marker,
                left: markerRect.left - rootRect.left,
                top: markerRect.top - rootRect.top,
                width: markerRect.width,
                height: markerRect.height,
                opacity: computedStyle.opacity === undefined ? 1 : parseFloat(computedStyle.opacity),
                paneZIndex: parentStyle && parentStyle.zIndex !== 'auto' ? parseFloat(parentStyle.zIndex) || 0 : 0,
                markerZIndex: computedStyle.zIndex !== 'auto' ? parseFloat(computedStyle.zIndex) || 0 : 0,
                visibleRegions: visibleRegions.map(function (region) {
                    return {
                        offsetX: region.left - markerRect.left,
                        offsetY: region.top - markerRect.top,
                        width: region.right - region.left,
                        height: region.bottom - region.top
                    };
                })
            });
        });

        if (!ignoredMarkers.length) {
            return noOpCapture;
        }

        drawableMarkers.sort(function (first, second) {
            return first.paneZIndex - second.paneZIndex || first.markerZIndex - second.markerZIndex;
        });
        return {
            paint: function (canvas) {
                if (!canvas || typeof canvas.getContext !== 'function' ||
                    !rootRect.width || !rootRect.height) {
                    return;
                }
                var context = canvas.getContext('2d');
                if (!context) {
                    return;
                }
                var scaleX = canvas.width / rootRect.width;
                var scaleY = canvas.height / rootRect.height;
                if (typeof context.save === 'function') {
                    context.save();
                }
                if (typeof context.setTransform === 'function') {
                    context.setTransform(scaleX, 0, 0, scaleY, 0, 0);
                } else if (typeof context.scale === 'function') {
                    context.scale(scaleX, scaleY);
                }
                drawableMarkers.forEach(function (marker) {
                    try {
                        context.globalAlpha = isNaN(marker.opacity) ? 1 : marker.opacity;
                        var regions = marker.visibleRegions;
                        var fullMarkerVisible = regions.length === 1 &&
                            regions[0].offsetX === 0 && regions[0].offsetY === 0 &&
                            regions[0].width === marker.width && regions[0].height === marker.height;
                        if (fullMarkerVisible) {
                            context.drawImage(
                                marker.element,
                                marker.left,
                                marker.top,
                                marker.width,
                                marker.height
                            );
                        } else {
                            var sourceWidth = marker.element.naturalWidth || marker.width;
                            var sourceHeight = marker.element.naturalHeight || marker.height;
                            var sourceScaleX = sourceWidth / marker.width;
                            var sourceScaleY = sourceHeight / marker.height;
                            regions.forEach(function (region) {
                                context.drawImage(
                                    marker.element,
                                    region.offsetX * sourceScaleX,
                                    region.offsetY * sourceScaleY,
                                    region.width * sourceScaleX,
                                    region.height * sourceScaleY,
                                    marker.left + region.offsetX,
                                    marker.top + region.offsetY,
                                    region.width,
                                    region.height
                                );
                            });
                        }
                    } catch (error) {
                        // A single unsupported icon must not abort the complete map export.
                    }
                });
                context.globalAlpha = 1;
                if (typeof context.restore === 'function') {
                    context.restore();
                }
            },
            restore: function () {},
            shouldIgnore: function (element) {
                return ignoredMarkers.indexOf(element) !== -1;
            }
        };
    }

    function loadHtml2Canvas(source) {
        if (typeof window === 'undefined') {
            return Promise.reject(new Error('html2canvas is available only in a browser'));
        }
        if (typeof window.html2canvas === 'function') {
            return Promise.resolve(window.html2canvas);
        }

        return new Promise(function (resolve, reject) {
            var scriptId = 'widget-map-html2canvas-loader';
            var existing = document.getElementById(scriptId);
            var handleLoad = function () {
                if (typeof window.html2canvas === 'function') {
                    resolve(window.html2canvas);
                } else {
                    reject(new Error('html2canvas did not expose its browser API'));
                }
            };
            if (existing) {
                existing.addEventListener('load', handleLoad, {once: true});
                existing.addEventListener('error', function () {
                    reject(new Error('Unable to load html2canvas'));
                }, {once: true});
                return;
            }
            var script = document.createElement('script');
            script.id = scriptId;
            script.src = source;
            script.onload = handleLoad;
            script.onerror = function () {
                reject(new Error('Unable to load html2canvas'));
            };
            document.head.appendChild(script);
        });
    }

    function waitForSaveDialogHandoff(targetWindow, timeoutMs) {
        if (!targetWindow || typeof targetWindow.addEventListener !== 'function' ||
            typeof targetWindow.removeEventListener !== 'function') {
            return Promise.resolve('unsupported');
        }

        var cancel = function () {};
        var handoff = new Promise(function (resolve) {
            var completed = false;
            var timeout = setTimeout(function () {
                complete('timeout');
            }, typeof timeoutMs === 'number' ? Math.max(0, timeoutMs) : 4000);

            function complete(reason) {
                if (completed) {
                    return;
                }
                completed = true;
                clearTimeout(timeout);
                targetWindow.removeEventListener('blur', handleBlur);
                resolve(reason);
            }

            function handleBlur() {
                complete('blur');
            }

            cancel = function () {
                complete('cancelled');
            };
            targetWindow.addEventListener('blur', handleBlur);
        });
        handoff.cancel = cancel;
        return handoff;
    }

    function saveBlob(blob, filename, options) {
        options = options || {};
        var url = URL.createObjectURL(blob);
        var anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = filename;
        anchor.style.display = 'none';
        var attached = false;
        var handoff = Promise.resolve('not-requested');
        try {
            document.body.appendChild(anchor);
            attached = true;
            if (options.waitForDialog) {
                handoff = waitForSaveDialogHandoff(
                    options.windowObject || (typeof window !== 'undefined' ? window : null),
                    options.dialogTimeoutMs
                );
            }
            anchor.click();
            document.body.removeChild(anchor);
            attached = false;
        } catch (error) {
            if (attached) {
                try {
                    document.body.removeChild(anchor);
                } catch (ignored) {}
            }
            if (typeof handoff.cancel === 'function') {
                handoff.cancel();
            }
            URL.revokeObjectURL(url);
            throw error;
        }
        setTimeout(function () {
            URL.revokeObjectURL(url);
        }, 0);
        return handoff;
    }

    function saveJson(value, filename) {
        saveBlob(
            new Blob([JSON.stringify(value, null, 2)], {type: 'application/geo+json;charset=utf-8'}),
            filename
        );
    }

    function saveCanvas(canvas, format, filename, options) {
        options = options || {};
        var mimeType = format === 'jpeg' ? 'image/jpeg' : 'image/png';
        var quality = format === 'jpeg' ? 0.92 : undefined;
        return new Promise(function (resolve, reject) {
            try {
                canvas.toBlob(function (blob) {
                    if (!blob) {
                        reject(new Error('The browser could not encode the map image'));
                        return;
                    }
                    try {
                        if (typeof options.onDownloadRequested === 'function') {
                            options.onDownloadRequested();
                        }
                        Promise.resolve(saveBlob(blob, filename, {
                            waitForDialog: true,
                            windowObject: options.windowObject,
                            dialogTimeoutMs: options.dialogTimeoutMs
                        })).then(resolve, reject);
                    } catch (error) {
                        reject(error);
                    }
                }, mimeType, quality);
            } catch (error) {
                reject(error);
            }
        });
    }

    function captureElement(rootElement, html2canvas, options) {
        options = options || {};
        var markerCapture;
        try {
            markerCapture = prepareLeafletMarkersForCapture(rootElement);
        } catch (error) {
            return Promise.reject(error);
        }
        var restored = false;
        var restore = function () {
            if (restored) {
                return;
            }
            restored = true;
            markerCapture.restore();
        };
        var scale = typeof options.scale === 'number' && isFinite(options.scale) && options.scale > 0 ?
            options.scale : Math.min(window.devicePixelRatio || 1, 2);
        var capture;
        try {
            capture = html2canvas(rootElement, {
                allowTaint: false,
                backgroundColor: '#ffffff',
                logging: false,
                scale: scale,
                useCORS: true,
                ignoreElements: function (element) {
                    return isControlExcludedFromCapture(element) ||
                        markerCapture.shouldIgnore(element);
                }
            });
        } catch (error) {
            restore();
            return Promise.reject(error);
        }
        return Promise.resolve(capture).then(function (canvas) {
            try {
                markerCapture.paint(canvas);
                return canvas;
            } finally {
                restore();
            }
        }, function (error) {
            restore();
            throw error;
        });
    }

    return {
        CAPTURE_HIDDEN_SELECTORS: CAPTURE_HIDDEN_SELECTORS,
        calculateControlRightOffset: calculateControlRightOffset,
        setExportBusyState: setExportBusyState,
        createVisibleFeatureCollection: createVisibleFeatureCollection,
        captureElement: captureElement,
        findCorsFailures: findCorsFailures,
        findCorsImageSamples: findCorsImageSamples,
        testCorsImage: testCorsImage,
        loadHtml2Canvas: loadHtml2Canvas,
        saveCanvas: saveCanvas,
        saveJson: saveJson
    };
}));
