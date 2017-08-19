<?php

class Account extends Model
{
    private function initalizeAccounts()
    {
        if (!$this->tableExists()) {
            self::getDatabaseInstance()->query('CREATE TABLE accounts(id INT(11) AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            salt VARCHAR(255) NOT NULL
            );');
        }
    }

    public function createAccount($username, $password)
    {
        $this->initalizeAccounts();
        $salt = Math::randomString(16);
        $password = hash('sha256', $password . $salt);

        return $this->insert()->set('username', $username)->set('password', $password)->set('salt', $salt)->execute();
        //return $this->query('INSERT INTO accounts(username,password,salt)VALUES(?,?,?)',
        //    array($username, $password, $salt)) !== false;
    }

    public function deleteAccountById($id)
    {
        $this->initalizeAccounts();
        return $this->delete()->where('id=?')->bind($id)->execute();
        //return $this->query('DELETE FROM accounts WHERE id = ?', array($id)) !== false;
    }

    public function deleteAccountByUsername($username)
    {
        $this->initalizeAccounts();
        return $this->delete()->where('username=?')->bind($username)->execute();
        //return $this->query('DELETE FROM accounts WHERE username = ?', array($username)) !== false;
    }

    public function getUsernameOfId($id)
    {
        $data = $this->select()->where('id=?')->limit(1)->bind($id)->execute();
        //$data = $this->query('SELECT username FROM accounts WHERE id = ? LIMIT 1', array($id));
        if (isset($data[0])) {
            return $data[0]['username'];
        }

        return null;
    }

    public function changeUsernameOfId($id, $newUsername)
    {
        $this->initalizeAccounts();

        return $this->update()->set('username', $newUsername)->set('id', $id)->execute();
        //return $this->query('UPDATE accounts SET username = ? WHERE id = ?', array($newUsername, $id)) !== false;
    }

    public function changePasswordOfId($id, $newPassword)
    {
        $this->initalizeAccounts();
        $salt = Math::randomString(16);
        $newPassword = hash('sha256', $newPassword . $salt);

        return $this->update()->set('password', $newPassword)->set('salt', $salt)->where('id=?')->bind($id)->execute();
        //return $this->query('UPDATE accounts SET password=?, salt=? WHERE id=?', array($newPassword, $salt, $id)) !== false;
    }

    public function changePasswordOfUsername($username, $newPassword)
    {
        $this->initalizeAccounts();
        $salt = Math::randomString(16);
        $newPassword = hash('sha256', $newPassword . $salt);

        return $this->update()->set('password', $newPassword)->set('salt', $salt)->where('username=?')->bind($username)->execute();
    }

    public function selectByUsername($username)
    {
        $this->initalizeAccounts();
        $data = $this->select()->where('username=?')->limit(1)->bind($username)->execute();
        //$data = $this->query('SELECT * FROM accounts WHERE username = ? LIMIT 1', array($username));
        if (isset($data[0])) {
            return $data[0];
        }

        return false;
    }

    public function selectById($id)
    {
        $this->initalizeAccounts();
        $data = $this->select()->where('id = ?')->limit(1)->bind($id)->execute();//('SELECT * FROM accounts WHERE id = ? LIMIT 1', array($id));
        if (isset($data[0])) {
            return $data[0];
        }

        return false;
    }

    public function selectAll()
    {
        $this->initalizeAccounts();

        return $this->select()->execute();
        //return $this->query('SELECT * FROM accounts');
    }

    public function existsByUsername($username)
    {
        $data = $this->select()->where('username=?')->bind($username)->execute();
        //$data = $this->query('SELECT id FROM accounts WHERE username = ? LIMIT 1', array($username));

        return isset($data[0]);
    }

    public function existsById($id)
    {
        $data = $this->select()->where('id=?')->limit(1)->bind($id)->execute();
        //$data = $this->query('SELECT id FROM accounts WHERE id = ? LIMIT 1', array($id));

        return isset($data[0]);
    }

    public function canLogin($username, $password)
    {
        $data = $this->select()->where('username=?')->limit(1)->bind($username)->execute();
        //$data = $this->query('SELECT password,salt FROM accounts WHERE username=? LIMIT 1',
        //    array($username));
        if (isset($data[0])) {
            $testPassword = hash('sha256', $password . $data[0]['salt']);
            if ($testPassword === $data[0]['password']) {
                return true;
            }
        }

        return false;
    }

}
