<?php

namespace Controllers;

use PDO;
use PDOException;

class Employee{
    public function __construct()
    {

    }

    function getEmployees() {

        if (isset($_GET['name'])) {
            return getEmployeesByName($_GET['name']);
        } else if (isset($_GET['modifiedSince'])) {
            return getModifiedEmployees($_GET['modifiedSince']);
        }

        $sql = "select e.id, e.firstName, e.lastName, e.title, count(r.id) reportCount " .
            "from employee e left join employee r on r.managerId = e.id " .
            "group by e.id order by e.lastName, e.firstName";
        try {
            $db = $this->getConnection();
            $stmt = $db->query($sql);
            $employees = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // Include support for JSONP requests
            if (!isset($_GET['callback'])) {
                echo json_encode($employees);
            } else {
                echo $_GET['callback'] . '(' . json_encode($employees) . ');';
            }

        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
        echo "go";
    }

    function getEmployee($id) {
        $sql = "select e.id, e.firstName, e.lastName, e.title, e.officePhone, e.cellPhone, e.email, e.managerId, e.twitterId, CONCAT(m.firstName, ' ', m.lastName) managerName, count(r.id) reportCount " .
            "from employee e " .
            "left join employee r on r.managerId = e.id " .
            "left join employee m on e.managerId = m.id " .
            "where e.id=:id";
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $employee = $stmt->fetchObject();
            $db = null;

            // Include support for JSONP requests
            if (!isset($_GET['callback'])) {
                echo json_encode($employee);
            } else {
                echo $_GET['callback'] . '(' . json_encode($employee) . ');';
            }

        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    function getReports($id) {

        $sql = "select e.id, e.firstName, e.lastName, e.title, count(r.id) reportCount " .
            "from employee e left join employee r on r.managerId = e.id " .
            "where e.managerId=:id " .
            "group by e.id order by e.lastName, e.firstName";

        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // Include support for JSONP requests
            if (!isset($_GET['callback'])) {
                echo json_encode($employees);
            } else {
                echo $_GET['callback'] . '(' . json_encode($employees) . ');';
            }

        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    function getEmployeesByName($name) {
        $sql = "select e.id, e.firstName, e.lastName, e.title, count(r.id) reportCount " .
            "from employee e left join employee r on r.managerId = e.id " .
            "WHERE UPPER(CONCAT(e.firstName, ' ', e.lastName)) LIKE :name " .
            "group by e.id order by e.lastName, e.firstName";
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($sql);
            $name = "%".$name."%";
            $stmt->bindParam("name", $name);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // Include support for JSONP requests
            if (!isset($_GET['callback'])) {
                echo json_encode($employees);
            } else {
                echo $_GET['callback'] . '(' . json_encode($employees) . ');';
            }

        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    function getModifiedEmployees($modifiedSince) {
        if ($modifiedSince == 'null') {
            $modifiedSince = "1000-01-01";
        }
        $sql = "select * from employee WHERE lastModified > :modifiedSince";
        try {
            $db = $this->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("modifiedSince", $modifiedSince);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;

            // Include support for JSONP requests
            if (!isset($_GET['callback'])) {
                echo json_encode($employees);
            } else {
                echo $_GET['callback'] . '(' . json_encode($employees) . ');';
            }

        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    function getConnection() {
        $dbhost="127.0.0.1";
        $dbuser="root";
        $dbpass="";
        $dbname="directory";
        $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbh;
    }
}