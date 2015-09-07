<?


// Fonction normalisant le test, c'est-à-dire retirant les majuscules et les accents.
function normaliser($string)
{ 
        $a = 'âäàéèëêîïûüç';
        $b = 'aaaeeeeiiuuc'; 
        $string = utf8_decode($string);     
        $string = strtr($string, utf8_decode($a), $b); 
        $string = strtolower($string); 
        return utf8_encode($string); 
}

