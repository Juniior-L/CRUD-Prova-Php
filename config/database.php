<?php
require_once '../../CRUD-Prova-Php/config/config.php';

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $this->pdo = new PDO(DB_DSN);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->createTableTasks();
            $this->createTableUsers();
            // $this->alterTable();
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function alterTable()
    {
        $sql = "ALTER TABLE tasks ADD COLUMN user_id INTEGER DEFAULT 0;";
        $this->pdo->exec($sql);
    }
    private function createTableUsers()
    {
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
        $tableExists = $stmt->fetch() !== false;

        if (!$tableExists) {
            $sql = "CREATE TABLE users ( 
                    user_id INTEGER DEFAULT 0,
                    id INTEGER PRIMARY KEY AUTOINCREMENT, 
                    nome TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    senha TEXT NOT NULL
                )";
            $this->pdo->exec($sql);
        }
    }
    private function createTableTasks()
    {
        $stmt = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='tasks'");
        $tableExists = $stmt->fetch() !== false;

        if (!$tableExists) {
            $sql = "CREATE TABLE IF NOT EXISTS tasks (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    note TEXT NOT NULL,
                    register TEXT NOT NULL
                )";
            $this->pdo->exec($sql);
        }
    }


    public function tableExists($table)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=:table");
            $stmt->execute(['table' => $table]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create($table, $arr)
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe");
            }

            $arr['register'] = date('Y-m-d H:i:s');
            $fields = array_keys($arr);
            $placeholders = array_map(fn($field) => ':' . $field, $fields);
            $sql = "INSERT INTO $table (" . implode(', ', $fields) . ")
                VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($arr);

            if ($result) {
                return [
                    'success' => true,
                    'id' => $this->pdo->lastInsertId(),
                    'message' => 'Registro criado com sucesso!'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erro ao criar o registro'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function read($table, $conditions = [], $limit = null, $offset = null)
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            $sql = "SELECT * FROM $table";
            $params = [];

            if (!empty($conditions)) {
                $where_conditions = [];
                foreach ($conditions as $field => $value) {
                    $placeholder = ":where_$field";
                    $where_conditions[] = "$field = $placeholder";
                    $params[$placeholder] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                $params[':limit'] = (int) $limit;

                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                    $params[':offset'] = (int) $offset;
                }
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'count' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function update($table, $data, $conditions = [])
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            $sql = "UPDATE $table SET ";
            $params = [];

            // SET
            $set_fields = [];
            foreach ($data as $field => $value) {
                $placeholder = ":set_$field";
                $set_fields[] = "$field = $placeholder";
                $params[$placeholder] = $value;
            }
            $sql .= implode(", ", $set_fields);

            // WHERE
            if (!empty($conditions)) {
                $where_conditions = [];
                foreach ($conditions as $field => $value) {
                    $placeholder = ":where_$field";
                    $where_conditions[] = "$field = $placeholder";
                    $params[$placeholder] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) alterado(s) com sucesso!' : 'Nenhum registro foi alterado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function delete($table, $conditions = [])
    {
        try {
            if (!$this->tableExists($table)) {
                throw new Exception("Tabela $table não existe!");
            }

            $sql = "DELETE FROM $table";
            $params = [];

            if (!empty($conditions)) {
                $where_conditions = [];
                foreach ($conditions as $field => $value) {
                    $placeholder = ":where_$field";
                    $where_conditions[] = "$field = $placeholder";
                    $params[$placeholder] = $value;
                }
                $sql .= " WHERE " . implode(" AND ", $where_conditions);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return [
                'success' => true,
                'affected_rows' => $stmt->rowCount(),
                'message' => $stmt->rowCount() > 0 ? 'Registro(s) deletado(s) com sucesso!' : 'Nenhum registro foi deletado'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro no banco: ' . $e->getMessage()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }


}

$db = Database::getInstance();
