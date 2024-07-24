<?php
/*
/ Connect to the database
DB::connect('localhost', 'my_database', 'user', 'password');

// Set the table and insert a new record
DB::table('my_table')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Read records with conditions, order, limit, offset, selected columns, and join clauses
$users = DB::table('my_table')
            ->select(['my_table.name', 'my_table.email', 'profile.age'])
            ->join('profile', 'my_table.id = profile.user_id', 'LEFT')
            ->where('my_table.name', '=', 'John Doe')
            ->order('my_table.email', 'DESC')
            ->limit(10)
            ->offset(5)
            ->read();

// Update records with conditions
DB::table('my_table')
   ->where('name', '=', 'John Doe')
   ->update(['email' => 'john.doe@example.com']);

// Delete records with conditions
DB::table('my_table')
   ->where('name', '=', 'John Doe')
   ->delete();
*/

class DB
{
    private static $pdo;
    private $table;
    private $conditions = [];
    private $order = '';
    private $limit = '';
    private $offset = '';
    private $columns = '*';
    private $joins = [];

    public static function connect($host = null, $db = null, $user = null, $pass = null)
    {
        if (!self::$pdo) {
            if (!$host || !$db || !$user || !$pass) {
                throw new InvalidArgumentException("Database connection parameters are missing.");
            }

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }

        return new self();
    }

    public static function table($table)
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function select($columns)
    {
        if (is_array($columns)) {
            $this->columns = implode(', ', $columns);
        } else {
            $this->columns = $columns;
        }
        return $this;
    }

    public function insert(array $data)
    {
        $keys = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
        $sql = "INSERT INTO {$this->table} ($keys) VALUES ($placeholders)";
        $stmt = self::$pdo->prepare($sql);
        
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        if ($stmt->execute()) {
            return self::$pdo->lastInsertId();
        }

        return false;
    }

    public function where($column, $operator, $value)
    {
        $this->conditions[] = [$column, $operator, $value];
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->conditions[] = [$column, $operator, $value, 'OR'];
        return $this;
    }

    public function order($column, $direction = 'ASC')
    {
        $this->order = "ORDER BY $column $direction";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = "LIMIT $limit";
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = "OFFSET $offset";
        return $this;
    }

    public function join($table, $condition, $type = 'INNER')
    {
        $this->joins[] = "$type JOIN $table ON $condition";
        return $this;
    }

    private function buildConditions()
    {
        if (!$this->conditions) {
            return '';
        }

        $sqlConditions = array_map(function ($condition) {
            return "{$condition[0]} {$condition[1]} :{$condition[0]}";
        }, $this->conditions);

        return 'WHERE ' . implode(' AND ', $sqlConditions);
    }

    private function buildJoins()
    {
        return implode(' ', $this->joins);
    }

    public function read()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table} " . $this->buildJoins() . ' ' . $this->buildConditions() . " $this->order $this->limit $this->offset";
        $stmt = self::$pdo->prepare($sql);

        foreach ($this->conditions as $condition) {
            $stmt->bindParam(":{$condition[0]}", $condition[2]);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function first()
    {
        $sql = "SELECT {$this->columns} FROM {$this->table} " . $this->buildJoins() . ' ' . $this->buildConditions() . " $this->order LIMIT 1";
        $stmt = self::$pdo->prepare($sql);

        foreach ($this->conditions as $condition) {
            $stmt->bindParam(":{$condition[0]}", $condition[2]);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(array $data)
    {
        $setClause = implode(', ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys($data)));

        $sql = "UPDATE {$this->table} SET $setClause " . $this->buildConditions();
        $stmt = self::$pdo->prepare($sql);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        foreach ($this->conditions as $condition) {
            $stmt->bindParam(":{$condition[0]}", $condition[2]);
        }

        return $stmt->execute();
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} " . $this->buildConditions();
        $stmt = self::$pdo->prepare($sql);

        foreach ($this->conditions as $condition) {
            $stmt->bindParam(":{$condition[0]}", $condition[2]);
        }

        return $stmt->execute();
    }
}