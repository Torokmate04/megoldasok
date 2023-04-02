<?php
include "./homework_input.php";
function Pontszamkalk(array $tomb):string{
    $kotelezo = "";
    $valaszthato = array();
    $alap_pontok = 0;
    $extra_pontok = 0;
    $kotelezomindenkinek = array("magyar nyelv és irodalom","történelem","matematika");
    $kotelezoangemelt = false;
    $valasztott = false;
    $ellenoriz = "";
    $kotelezoellenorzoszam = 0;
    $valaszthatoellenorzoszam = 0;
    $valasztottszam = 0;
    
    
    foreach($tomb as $key => $value){ 
        if($key == "valasztott-szak"){
            
            foreach($value as $szaknevek){
                
                if($szaknevek == 'ELTE' || $szaknevek== 'IK' || $szaknevek == 'Programtervező informatikus'){
                    $kotelezo = "matematika";
                    array_push($valaszthato, "biologia", "fizika", "informatika", "kemia");
                    break;
                    //itt megadtuk a valaszthato tantargyakat
                }
                if($szaknevek == "PPKE" || $szaknevek == "BTK" || $szaknevek == "Aglisztika"){
                    $kotelezo = "angol";
                    array_push($valaszthato, "francia", "német", "olasz", "orosz", "spanyol", "történelem");
                    break;
                    
                    //itt megadtuk a valaszthato tantargyakat
                }
                
                //return ("Nem megfelelo a valasztott szak vagy a hozzátartozo nevek!");
                //ellenoriztuk hogy jok e a szakmai nevek
            }
        }
        if($key == "erettsegi-eredmenyek" ){
            
            
            foreach($value as $eredmenyek){
                $pontszam = rtrim($eredmenyek['eredmeny'],'%');
               
                if($pontszam < 20){
                    return("hiba, nem lehetséges a pontszámítás".$eredmenyek['nev']."tárgyból elért 20% alatti eredmény miatt");
                }
                
                if($eredmenyek['tipus'] == "emelt" && $pontszam  > 20){
                    $extra_pontok += 50;
                }
                if($kotelezo == $eredmenyek['nev']){
                    $alap_pontok += intval(str_replace('%', '', $eredmenyek['eredmeny']));
                    // itt meg a kotelezo pontszamot adtuk hozza az alappontokhoz
                }
                if(in_array($eredmenyek['nev'],$valaszthato)){
                    if($eredmenyek['eredmeny'] > $valasztottszam){
                        $alap_pontok += intval(str_replace('%', '', $eredmenyek['eredmeny'])); // alappontokhoz hozza adjuk a eredmeny a %jel levetelevel
                        $alap_pontok -= $valasztottszam; // itt ki vonjuk a valasztott szamot amit akovi sorba megadunk de alapbol 0 ezert az elso ciklusnal semmin nem valtoztat
                        $valasztottszam = intval(str_replace('%', '', $eredmenyek['eredmeny'])); // itt megadjuk hogy mennyi volt a szám a kovetkezo tombben 
                      //igy ha nagyobb lesz a kovetkezo szám akkor ki vonja az elozot es hozza adja a nagyobbat igy csak a legnagyobb lesz hozza adva!
                    }
                }
                
                
                //ezzel ki szamoljuk az osszes eretsegi eredmenyet mar csak ellenorizni kell hogy mikbol eretsegizet!
                   
                    if(in_array($eredmenyek['nev'],$kotelezomindenkinek)){
                        $ellenoriz .= $eredmenyek['nev'] . ",";
                        $kotelezoellenorzoszam++;
                        //echo $kotelezoellenorzoszam."<br>";
                    }
                    
                    if (in_array($eredmenyek['nev'], $valaszthato)) {
                        $valasztott = true;
                        $valaszthatoellenorzoszam++;
                        
                    }
                    
                    if($kotelezo == "angol"){
                        if($eredmenyek['nev'] == $kotelezo){
                            if($eredmenyek['tipus'] == "emelt"){
                                $kotelezoangemelt = true;
                            }
                        }
                    }
            }
                if($kotelezoellenorzoszam != 3){
                    return("hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt");
                 }
                if($valaszthatoellenorzoszam == 0){
                    return("hiba, nem lehetséges a pontszámitas a kötelezően valasztható éretségi tantargy(ak) hianya miatt");
                }
                if($valasztott != true){
                    return("hiba, nem lehetséges a pontszámítás a kötelezően választható érettségi tárgyak hiánya miatt!");
                }
                if($kotelezo == "angol"){
                    if($kotelezoangemelt == false){
                        return("hiba, nem lehetséges a pontszámitás a kőtelező $kotelezo (emelt szinten) hianya miatt!");
                    }
                }
            //mindent ellenoriztunk az alap_pontszamok mar megvannak és ha volt emelt akkor hozza adtuk az 50pluszpontot
        }
        if($key == "tobbletpontok"){
            $nyelv = array();
            $ugyanolyannyelv = false;
            foreach($value as $tobbletek){
                if(in_array($tobbletek['nyelv'],$nyelv)){
                    $ugyanolyannyelv = true;
                }   
                else{
                    array_push($nyelv,$tobbletek['nyelv']);
                }
                //ellenorizzuk hogy nem tett e két ugyanolyan nyelvbol nyelvizsgat 
                    switch($tobbletek['tipus']){
                        case "B2":
                            $extra_pontok += 28;
                            break;
                        
                        case "C1" :
                            $extra_pontok += 40;
                            break;
                    }
                    // itt hozza adjuk a pontokat amiket a nyelvvizsgáért kapot
                if($ugyanolyannyelv){
                    $extra_pontok += -28;
                    //itt meg ha két ugyanolyan nyelvbol tette akkor le vonom a 28pontot a gyengéb nyelvvizsgáért igy csak 40 pontot ér a két ugyanolyan nyelvvizsga
                }
                if($extra_pontok > 100){
                    $extra_pontok = 100;
                    // szaz pontnal tobb extra pontot nem kaphat egy ember
                }
            }
        }

    }
    $osszeg = ($alap_pontok * 2) + $extra_pontok;
    //476 (376 alappont + 100 többletpont)
    return "$osszeg (" . $alap_pontok * 2 . " alappont + $extra_pontok tobletpont)";
}
echo Pontszamkalk($exampleData);
echo ("<br>");
echo Pontszamkalk($exampleData1);
echo ("<br>");
echo Pontszamkalk($exampleData2);
echo ("<br>");
echo Pontszamkalk($exampleData3);
echo ("<br>");
?>
