<?php

namespace App\Services;

use function Webmozart\Assert\Tests\StaticAnalysis\length;

class Generator
{
    public function generateAccountNumber(){
        $codeBank = "007";
        $codeGuichet = str_pad(random_int(1, 99), 2, "0");
        $numero = str_pad(random_int(1, 9999999999), 10, "0");

        $numeroCompte = $codeBank.$codeGuichet.$numero;
        $coef = [7,3,1,7,3,1,7,3];
        $somme = 0;
        for ($i=0; $i < strlen($numeroCompte); $i++){
            $somme += $numeroCompte[$i]*$coef[$i % 8];
        }
        $cle = ((10 - $somme%10) % 10);
        return $numeroCompte.strval($cle);
    }

    public function generateCreditCard(){
        return str_pad(random_int(50, 59), 4, strval(random_int(0,9))).' '.strval(random_int(1000,9999)).' '.strval(random_int(1000,9999)).' '.strval(random_int(1000,9999));
    }

}