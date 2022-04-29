<?php
$GLOBALS['array_of_number'] = [2,3,4,5,6,7,8,9];
class reseau {
   
    private $ip;
    private $cidr;
    private $mask;
    private $addrReseau;
    private $addrBc;
    private $firstAddr;
    private $lastAddr;
    private $nbHote;
    private $object;


    public function __construct($cidr, $ip) {
        $this->ip = $ip;
        $this->cidr = $cidr;
        if($this->verifyIp() == false || $this->verifyCidr() == false) {return "Erreur";}
        $this->mask = $this->getDecMask($cidr);
        $this->addrReseau = $this->getAddressReseau();
        $this->addrBc = $this->getAddressBroad();
        $this->firstAddr = $this->getFirstAddress();
        $this->lastAddr = $this->getLastAddress();
        $this->nbHote = $this->hoteNb();
        $this->object = $this->getObjectIp();
    }
    
    public function verifyIp() {
        return filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? true : false;
    }
    
    public function verifyCidr() {
        return $this->cidr >= 1 && $this->cidr <= 30;
    }
    
    private function getObjectIp() {
        return [
            "cidr" => $this->cidr,
            "mask" => $this->mask,
            "addrReseau" => $this->addrReseau,
            "addrBc" => $this->addrBc,
            "firstAddr" => $this->firstAddr,
            "lastAddr" => $this->lastAddr,
            "nbHote" => $this->nbHote,
                ];
    }
    
    
    
    
    private function bin2dec($bin, $nb = 0) {
        $bin = strrev($bin);
        $tab = str_split($bin);
        if(count($tab) > 8 || count($tab) < 8 || array_intersect($GLOBALS['array_of_number'], $tab)) return false;
        for($i = 0; $i < 8; $i++) {$tab[$i] == 1 ? $nb = $nb + pow(2, $i) : $nb;}
        return $nb;
    }
    
    private function dec2bin($dec, $text = "") {    
        if($dec < 0 || $dec > 255  || !ctype_digit($dec)) {return "";}
            for($i = 0; $i < 8; $i++) {
                $binaire = $dec % 2;
                $dec = round($dec / 2, 0, PHP_ROUND_HALF_DOWN);
                $text .= $binaire;
            }
        return strrev($text);
    }
    
    private function IpToBin($ip) {
        $ip = $ip = explode(".", $ip);
        $bin = array();
        foreach($ip as $value) {
            $result = dec2bin($value, $text = "");
            $result = $result = chunk_split($result, 4, '');
            array_push($bin, $result);
        }
        $ip = $ip = implode("",$bin); 
        return $ip;
    }
    
    private function getDecMask($cidr) {
        $mask = "";
        $ip = "";
        for($i = 0; $i <= 32;$i++) {
            $i <= $cidr ? $ip .= "1" : $ip .= "0";
            if($i % 8 == 0 ) {
                $mask .= $this->bin2dec($ip, $nb = 0);
                    if($i % 8 == 0 && $i != 0 && $i !=32 ) {$mask .= ".";}
                $ip = "";
            }
        }
        return $mask;
    }
    
    private function getAddressReseau() {
       return long2ip(ip2long($this->ip) & ip2long($this->mask));
    }
    
    private function getAddressBroad() {
       return long2ip(ip2long($this->ip) | ip2long(long2ip(~ip2long($this->mask))));
    }
    
    private function getFirstAddress() {
        $ipExplode = explode(".", $this->addrReseau);
        $ipExplode[3] = $ipExplode[3] + 1;
        return $firstIp = implode(".", $ipExplode);
    }
    
    private function getLastAddress() {
        $ipExplode2 = explode(".", $this->addrBc);
        $ipExplode2[3] = $ipExplode2[3] - 1;
        return $lastIp = implode(".", $ipExplode2);  
    }
    
    private function hoteNb() {
       return (pow(2, 32-$this->cidr)-2) <= 0 ? 0 : (pow(2, 32-$this->cidr)-2);
     }

    public function __get($name) {
        return isset($this->$name) ? $this->$name : false;
    }
    
    public function __set($name, $value) {
        return isset($this->$name) ? $this->$name = $value : false;
    }  
}
?>
