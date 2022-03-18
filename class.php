<?php
$GLOBALS['array_of_number'] = [2,3,4,5,6,7,8,9];

class reseau {
 
    private $ip;
    private $mask;
    private $addrReseau;
    private $addrBc;
    private $firstAddr;
    private $lastAddr;
    private $nbHote;
    private $cidr;
 
    public function __construct($cidr, $ip) {
        $this->ip = $ip;
        $this->mask = $this->build_Mask($cidr);
        $this->addrReseau = $this->buildAddrReseau();
        $this->addrBc = $this->buildAddrBc();
        $this->firstAddr = $this->buildFirstAddr();
        $this->lastAddr = $this->buildLastAddr();
        $this->nbHote = $this->hoteNb();
        $this->cidr = $cidr;
    }
 
    private function build_Mask($cidr) {
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
 
    private function bin2dec($bin, $nb = 0) {
        $bin = strrev($bin);
        $tab = str_split($bin);
        if(count($tab) > 8 || count($tab) < 8 || array_intersect($GLOBALS['array_of_number'], $tab)) return false;
        for($i = 0; $i < 8; $i++) {$tab[$i] == 1 ? $nb = $nb + pow(2, $i) : $nb;}
        return $nb;
}
 
    private function buildAddrReseau() {
       return long2ip(ip2long($this->ip) & ip2long($this->mask));
    }
 
    private function buildAddrBc() {
       return long2ip(ip2long($this->ip) | ip2long(long2ip(~ip2long($this->mask))));
    }
 
    private function buildFirstAddr() {
        $ipExplode = explode(".", $this->addrReseau);
        $ipExplode[3] = $ipExplode[3] + 1;
        return $firstIp = implode(".", $ipExplode);
    }
 
    private function buildLastAddr() {
        $ipExplode2 = explode(".", $this->addrBc);
        $ipExplode2[3] = $ipExplode2[3] - 1;
        return $lastIp = implode(".", $ipExplode2);  
    }
 
   private function hoteNb() {
     return $nb_host = (pow(2, 32-$this->cidr)-2) <= 0 ? 0 : (pow(2, 32-$this->cidr)-2);
   }
 
    public function __get($name) {
        return isset($this->$name) ? $this->$name : false;
    }
 
    public function __set($name, $value) {
        return isset($this->$name) ? $this->$name = $value : false;
    }  
}
?>
