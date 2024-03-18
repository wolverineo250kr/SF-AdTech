<?php

namespace Interfaces;

interface UserModelInterface
{
    public function getUsersWithPagination();
    public function countUsers();
    public function updateUser();
    public function getOffersWithPaginationWebmaster($perPage, $offset, $webmasterId);
    public function changeStatus();
    public function getUser();
    public function setId($id);
    public function setPerPage($value);
    public function setOffset($value);
    public function setUsername($username);
    public function setStatus($is_active);
    public function setRoleId($role_id);
    public function setEmail($email);
    public function setPassword($password);
}
 