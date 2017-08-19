<?php

/*
 The members system was already created from a classroom project.
 There is no need to re-invent the wheel.
 Although, you are allowed to edit the class.
*/

class MembersController extends Controller
{
    public function index()
    {
        $this->_lockWithPermission('members.view');
        $m = new Account();
        $items = $m->selectAll();
        $this->set('items', $items);
    }

    public function change($id, $column)
    {
        if (Session::getCurrentUser('id') !== $id) {
            $this->_lockWithPermission('members.edit', '/members/view/' . $id, 'You are not allowed to edit members!');
        }
        if ($this->is('post')) {
            $a = new Account();
            if ($column === 'password') {
                $userData = $_POST['Account'];
                if (isset($userData['password'])) {
                    $password = trim($userData['password']);
                    if (strlen($password) > 0) {
                        if ($a->changePasswordOfId($id, $password)) {
                            Session::setFlash('Changed password successfully!', 'success');
                        } else {
                            Session::setFlash('Unable to change password! Unknown error!', 'error');
                        }
                    } else {
                        Session::setFlash('You must type in a password!', 'warning');
                    }
                }
            } elseif ($column === 'role') {
                $r = new Role();
                if (!$r->isAllowed(Session::getCurrentUser('username'), 'members.editrole')) {
                    Session::setFlash('You are not allowed to change the roles!');

                    return $this->redirect('/members/view/' . $id);
                }
                $roleData = $_POST['Role'];
                if (isset($roleData['value'])) {
                    $role = $roleData['value'];
                    if (strlen($role) > 0) {
                        $r = new Role();
                        $r->giveRoleToAccount($a->getUsernameOfId($id), $role);
                        Session::setFlash('Added role successfully!', 'success');
                    } else {
                        Session::setFlash('You must type in a role!', 'warning');
                    }
                }
            } elseif ($column === 'removerole') {
                $r = new Role();
                if (!$r->isAllowed(Session::getCurrentUser('username'), 'members.editrole')) {
                    Session::setFlash('You are not allowed to change the roles!', 'warning');

                    return $this->redirect('/members/view/' . $id);
                }
                $roleData = $_POST['Role'];
                if (isset($roleData['value'])) {
                    $role = $roleData['value'];
                    if (strlen($role) > 0) {
                        $r = new Role();
                        $r->revokeRoleFromAccount($a->getUsernameOfId($id), $role);
                        Session::setFlash('Removed role successfully!', 'success');
                    } else {
                        Session::setFlash('You must type in a role!', 'warning');
                    }
                }
            }
        }

        return $this->redirect('/members/view/' . $id);
    }

    public function register()
    {
        $this->_lockWithPermission('members.register');
        if ($this->is('post')) {
            $account = $_POST['Account'];
            $a = new Account();
            if (isset($account['username']) && strlen($account['username']) > 0) {
                if (isset($account['username']) && strlen($account['password']) > 0) {
                    if ($account['password'] === $account['password2']) {
                        $a->createAccount($account['username'], $account['password']);
                         Session::setFlash('Created account successfully!', 'success');
                    } else {
                        Session::setFlash('You must retype the same password!', 'warning');
                    }
                } else {
                    Session::setFlash('You must type in a password!', 'warning');
                }
            } else {
                Session::setFlash('You must type in a username!', 'warning');
            }
        }
    }

    public function login()
    {
        if (Session::isLoggedIn()) {
            return $this->redirect('/');
        }
        if ($this->is('post')) {
            $account = $_POST['Account'];
            $a = new Account();
            if ($a->canLogin($account['username'], $account['password'])) {
                $data = $a->selectByUsername($account['username']);
                Session::setCurrentUser(array('id' => $data['id'], 'username' => $account['username']));
                Session::setFlash('You have been logged in successfully.', 'success');
                $this->redirect('/');
            } else {
                Session::setFlash('Invalid username or password!', 'warning');
            }
        }
    }

    public function logout()
    {
        if (Session::logout()) {
            Session::setFlash('You have been logged out!', 'success');
        }
        $this->redirect('/');
    }

    public function view($id)
    {
        if (!Session::getCurrentUser('id') === $id) {
            $this->_lockWithPermission('members.view', '/members', 'You are not allowed to view this account!');
        }
        $a = new Account();
        $item = $a->selectById($id);
        $r = new Role();
        $roles = $r->getRolesOfAccount($item['username']);
        $allRoles = $r->getAllRoles();
        $this->set('item', $item);
        $this->set('roles', $roles);
        $this->set('allRoles', $allRoles);
    }

    public function delete($id)
    {
        if ($this->is('post')) {
            $m = new Account();
            Session::setFlash('Deleted member successfully!', 'success');
        }
        $this->redirect('/members');
    }

}
