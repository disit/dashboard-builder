/* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

//this class provides a database connection pool, using org.apache.commons.dbcp and org.apache.commons.pool libraries
package utility;

import java.io.IOException;
import java.sql.Connection;
import java.util.Properties;
import org.apache.commons.dbcp.ConnectionFactory;
import org.apache.commons.dbcp.DriverManagerConnectionFactory;
import org.apache.commons.dbcp.PoolableConnectionFactory;
import org.apache.commons.dbcp.PoolingDataSource;
import org.apache.commons.pool.impl.GenericObjectPool;
import javax.sql.DataSource;

/**
 *
 * @author Daniele Cenni, daniele.cenni@unifi.it
 */
public class ConnectionPool {

  /**
   *
   */
  public static final String DRIVER = "com.mysql.jdbc.Driver";

  /**
   *
   */
  public String URL;

  /**
   *
   */
  public String USERNAME;

  /**
   *
   */
  public String PASSWORD;

  /**
   *
   */
  public int connections;

  private GenericObjectPool connectionPool = null;

  private static ConnectionPool connPool;
  private static DataSource dataSource;
  
  /**
   *
   * @param url
   * @param username
   * @param password
   * @throws IOException
   */
  public ConnectionPool(String url, String username, String password, int maxConnections) throws IOException {
    URL = url;
    USERNAME = username;
    PASSWORD = password;
    connections = maxConnections;
  }

  /**
   *
   * @return @throws Exception
   */
  public DataSource setUp() throws Exception {
    /**
     * Load JDBC Driver class.
     */
    Class.forName(ConnectionPool.DRIVER).newInstance();

    /**
     * Creates an instance of GenericObjectPool that holds our pool of
     * connections object.
     */
    connectionPool = new GenericObjectPool();
    // set the max number of connections
    connectionPool.setMaxActive(connections);
    // if the pool is exhausted (i.e., the maximum number of active objects has been reached), the borrowObject() method should simply create a new object anyway
    connectionPool.setWhenExhaustedAction(GenericObjectPool.WHEN_EXHAUSTED_GROW);

    /**
     * Creates a connection factory object which will be use by the pool to
     * create the connection object. We passes the JDBC url info, username and
     * password.
     */
    ConnectionFactory cf = new DriverManagerConnectionFactory(
            URL,
            USERNAME,
            PASSWORD);

    /**
     * Creates a PoolableConnectionFactory that will wraps the connection object
     * created by the ConnectionFactory to add object pooling functionality.
     */
    PoolableConnectionFactory pcf
            = new PoolableConnectionFactory(cf, connectionPool,
                    null, null, false, true);
    return new PoolingDataSource(connectionPool);
  }

  /**
   *
   * @return
   */
  public GenericObjectPool getConnectionPool() {
    return connectionPool;
  }

  // Prints connection pool status
  public void printStatus() {
    System.out.println("Max   : " + getConnectionPool().getMaxActive() + "; "
            + "Active: " + getConnectionPool().getNumActive() + "; "
            + "Idle  : " + getConnectionPool().getNumIdle());
  }

  public static Connection getConnection() {
    try {
      if (connPool == null) {
        Configuration conf = Configuration.getInstance();
        connPool = new ConnectionPool(conf.get("urlMySqlDB", "")+conf.get("dbMySql", "ServiceMap"), conf.get("userMySql", ""), conf.get("passMySql", ""),Integer.parseInt(conf.get("maxConnectionsMySql", "10")));
        if (dataSource == null) {
          dataSource = connPool.setUp();
        }
      }
      return dataSource.getConnection();
    } catch (IOException e) {
      e.printStackTrace();
      return null;
    } catch (Exception e) {
      e.printStackTrace();
      return null;
    }
  }
}
