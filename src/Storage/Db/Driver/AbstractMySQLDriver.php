<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Ceive\DataRecord\Storage\Db\Driver;

use Ceive\DataRecord\Storage\Db\Connection;
use Ceive\DataRecord\Storage\Db\Driver;
use Ceive\DataRecord\Storage\Db\Exception;
use Ceive\DataRecord\Storage\Db\Platforms\MySQLPlatform;

/**
 * Abstract base implementation of the {@link Doctrine\DBAL\Driver} interface for MySQL based drivers.
 *
 * @author Steve Müller <st.mueller@dzh-online.de>
 * @link   www.doctrine-project.org
 * @since  2.5
 */
abstract class AbstractMySQLDriver implements Driver, ExceptionConverterDriver
{
    /**
     * {@inheritdoc}
     *
     * @link http://dev.mysql.com/doc/refman/5.7/en/error-messages-client.html
     * @link http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
     */
    public function convertException($message, DriverException $exception)
    {
        switch ($exception->getErrorCode()) {
            case '1213':
                return new Exception\DeadlockException($message, $exception);
            case '1205':
                return new Exception\LockWaitTimeoutException($message, $exception);
            case '1050':
                return new Exception\TableExistsException($message, $exception);

            case '1051':
            case '1146':
                return new Exception\TableNotFoundException($message, $exception);

            case '1216':
            case '1217':
            case '1451':
            case '1452':
            case '1701':
                return new Exception\ForeignKeyConstraintViolationException($message, $exception);

            case '1062':
            case '1557':
            case '1569':
            case '1586':
                return new Exception\UniqueConstraintViolationException($message, $exception);

            case '1054':
            case '1166':
            case '1611':
                return new Exception\InvalidFieldNameException($message, $exception);

            case '1052':
            case '1060':
            case '1110':
                return new Exception\NonUniqueFieldNameException($message, $exception);

            case '1064':
            case '1149':
            case '1287':
            case '1341':
            case '1342':
            case '1343':
            case '1344':
            case '1382':
            case '1479':
            case '1541':
            case '1554':
            case '1626':
                return new Exception\SyntaxErrorException($message, $exception);

            case '1044':
            case '1045':
            case '1046':
            case '1049':
            case '1095':
            case '1142':
            case '1143':
            case '1227':
            case '1370':
            case '2002':
            case '2005':
                return new Exception\ConnectionException($message, $exception);

            case '1048':
            case '1121':
            case '1138':
            case '1171':
            case '1252':
            case '1263':
            case '1364':
            case '1566':
                return new Exception\NotNullConstraintViolationException($message, $exception);
        }

        return new Exception\DriverException($message, $exception);
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase(Connection $conn){
        $params = $conn->getParams();
        if (isset($params['dbname'])) {
            return $params['dbname'];
        }
        return $conn->query('SELECT DATABASE()')->fetchColumn();
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform(){
        return new MySQLPlatform();
    }
}
