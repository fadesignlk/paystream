<?php
// classes/Employee.php

require_once __DIR__ . '/../repositories/EmployeeRepository.php';


class Employee {
    private $employeeRepository;
    
    private $employeeId;
    private $name;
    private $rate;
    private $address;
    private $contact;
    private $type;
    private $dob;
    private $ni;
    private $paymentMethod;
    private $bankAccount;
    private $sortCode;
    private $status;

    public function __construct($dbHandler, $employeeId=null, $name=null, $rate=null, $address = null, $contact = null, $type = null, $dob = null, $ni = null, $paymentMethod = null, $bankAccount = null, $sortCode = null, $status = null) {
        $this->employeeRepository = new EmployeeRepository($dbHandler);
        $this->employeeId = $employeeId;
        $this->name = $name;
        $this->rate = $rate;
        $this->address = $address;
        $this->contact = $contact;
        $this->type = $type;
        $this->dob = $dob;
        $this->ni = $ni;
        $this->paymentMethod = $paymentMethod;
        $this->bankAccount = $bankAccount;
        $this->sortCode = $sortCode;
        $this->status = $status;
    }

    // ... (Existing getters)    
    public function getEmployeeId() { return $this->employeeId; }
    public function getName() { return $this->name; }
    public function getRate() { return $this->rate; }
    public function getAddress() { return $this->address; }
    public function getContact() { return $this->contact; }
    public function getType() { return $this->type; }
    public function getDob() { return $this->dob; }
    public function getNi() { return $this->ni; }
    public function getPaymentMethod() { return $this->paymentMethod; }
    public function getBankAccount() { return $this->bankAccount; }
    public function getSortCode() { return $this->sortCode; }
    public function getStatus() { return $this->status; }

    public function getEmployees($search = '', $status = '') {
        return $this->employeeRepository->getEmployees($search, $status);
    }

    public function getAllEmployees() {
        return $this->employeeRepository->getAllEmployees();
    }

    public function getEmployeeById($employeeId) {
        return $this->employeeRepository->getEmployeeById($employeeId);
    }

    public function addEmployee($employeeData, $updatedBy) {
        return $this->employeeRepository->addEmployee($employeeData, $updatedBy);
    }

    public function updateEmployee($employeeId, $updatedBy) {
        return $this->employeeRepository->updateEmployee($employeeId, $updatedBy);
    }

    public function deleteEmployee($employeeId, $updatedBy) {
        return $this->employeeRepository->deleteEmployee($employeeId, $updatedBy);
    }

    //Setters
    public function setName($name) { $this->name = $name; }
    public function setRate($rate) { $this->rate = $rate; }
    public function setAddress($address) { $this->address = $address; }
    public function setContact($contact) { $this->contact = $contact; }
    public function setType($type) { $this->type = $type; }
    public function setDob($dob) { $this->dob = $dob; }
    public function setNi($ni) { $this->ni = $ni; }
    public function setPaymentMethod($paymentMethod) { $this->paymentMethod = $paymentMethod; }
    public function setBankAccount($bankAccount) { $this->bankAccount = $bankAccount; }
    public function setSortCode($sortCode) { $this->sortCode = $sortCode; }
    public function setStatus($status) { $this->status = $status; }
}
?>