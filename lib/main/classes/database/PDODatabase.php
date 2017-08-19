<?php

class PDODatabase extends AbstractDatabase
{
    public static function bindToStatement(PDOStatement $stmt, array $bindVariables)
    {
        if (is_array($bindVariables)) {
            if (count(array_filter(array_keys($bindVariables), 'is_string'))) {
                foreach ($bindVariables as $key => $variable) {
                    $stmt->bindValue($key, $variable);
                }
            } else {
                $i = 0;
                $count = count($bindVariables);
                while ($i < $count) {
                    $stmt->bindValue($i + 1, $bindVariables[$i]);
                    $i++;
                }
            }
        }
    }

    private $pdoConnection;

    public function getConnection()
    {
        $this->initalizeConnection();

        return $this->pdoConnection;
    }

    public function lastInsertId()
    {
        $this->initalizeConnection();

        return $this->pdoConnection->lastInsertId();
    }

    public function query($query, array $context)
    {
        try {
            $stmt = $this->getConnection()->prepare($query);
            if ($stmt !== false) {
                PDODatabase::bindToStatement($stmt, $context);
                if ($stmt->execute()) {
                    if ($stmt->columnCount() !== 0) {
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($data !== false) {
                            return $data;
                        }
                    } else {
                        return true;
                    }
                }
                throw new DatabaseException(print_r($stmt->errorInfo(), true));
            }
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
        throw new DatabaseException(print_r($this->pdoConnection->errorInfo(), true));
    }
    
    public function execute(Query $queryObject)
    {
        return $this->query($queryObject->getSql(), $queryObject->getBinded());
    }

    private function initalizeConnection()
    {
        if ($this->pdoConnection === null) {
            try {
                $this->pdoConnection = new PDO($this->getConnectionString(), $this->getUsername(), $this->getPassword(), $this->getAllOptions());
            } catch (Exception $e) {
				$log = $this->getLogger();
				if ($log !== null) {
					$log->warning('Failed to connect to database. First try.', array($e));
				}
                try {
                    $this->pdoConnection = new PDO($this->getConnectionString(), $this->getUsername(), $this->getPassword(), $this->getAllOptions());
                } catch (Exception $e) {
					if ($log !== null) {
						$log->warning('Failed to connect to database. Second try.', array($e));
					}
					try {
						$this->pdoConnection = new PDO($this->getConnectionString(), $this->getUsername(), $this->getPassword(), $this->getAllOptions());
					} catch (PDOException $e) {
						$this->pdoConnection = null;
						throw new DatabaseException($e);
					}
                }
            }
            $this->pdoConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdoConnection->setAttribute(PDO::ATTR_TIMEOUT, 5);
        }
    }

}
