<?php

namespace model;
class UserLDAPRepository implements UserRepositoryReadInterface
{
    public static function ConnectLdap($name, $password, $ldapServer)
    {
        $qc_username = "qc\\";
        $instr_username = "instr\\";
        $name = trim(htmlspecialchars($name));
        $qc_username .= $name;
        $instr_username .= $name;
        $password = trim(htmlspecialchars($password));
        $ldap = ldap_connect($ldapServer);
        sleep(1);
        if ($bind = ldap_bind($ldap, $qc_username, $password)) {
            ldap_close($ldap);
            return true;
        } else if ($bind = ldap_bind($ldap, $instr_username, $password)) {
            ldap_close($ldap);
            return true;
        } else {
            ldap_close($ldap);
            return false;
        }
    }

    public static function fetchByEmail(\PDO $db, string $email)
    {
        // TODO: Implement fetchByEmail() method.
        // I don't think I need it but we will see
    }

    public function fetchUserByUsername($db, $username, $ldapBaseDN, $serviceUsername, $servicePassword)
    {
        $newUser = \model\User::create()
            ->setUsername($username)
            ->setEmail($this->ReturnEmailAddress($db, $username, $ldapBaseDN, $serviceUsername, $servicePassword))
            ->setDisplayname($this->ReturnDisplayName($db, $username, $ldapBaseDN, $serviceUsername, $servicePassword));
        return $newUser;
    }

    static function ReturnEmailAddress($db, $inputUsername, $ldapBaseDN, $serviceUsername, $servicePassword)
    {
        return self::fetchByUsername($db, $inputUsername)->ReturnParameter($inputUsername, "mail", $ldapBaseDN, $serviceUsername, $servicePassword);
    }

    function ReturnParameter($inputUsername, $inputParameter, $ldapServer, $service_username, $service_password)
    {
        $ldap = ldap_connect($ldapServer);
        if ($bind = ldap_bind($ldap, $service_username, $service_password)) {
            $result = ldap_search($ldap, "", "(CN=$inputUsername)") or die ("Error in search query: " . ldap_error($ldap));
            $data = ldap_get_entries($ldap, $result);
            if (isset($data[0][$inputParameter][0])) {
                return $data[0][$inputParameter][0];

            }
        }
        ldap_close($ldap);
        return "fail";
    }

    public static function ReturnDisplayName($db, $inputUsername, $ldap_baseDN, $service_username, $service_password)
    {
        return self::fetchByUsername($db, $inputUsername)->ReturnParameter($inputUsername, "displayname", $ldap_baseDN, $service_username, $service_password);
    }

    function IsNotNullOrEmptyString($question)
    {
        return (!isset($question) || trim($question) === '');
    }

}