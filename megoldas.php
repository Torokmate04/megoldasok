<?php 
include "./homework_input.php";
function calculatePoints(array $input):string{
    $mandatorySubject = '';
    $electiveSubjects = [];
    $basePoints = 0;
    $extraPoints = 0;
    $electiveSubjectsForEveryone = ['magyar nyelv és irodalom', 'történelem', 'matematika'];
    $mandatorySubjectElevated = false;
    $chosenSubject = false;
    $summaryOfSubjects = '';
    $numberOfMandatorySubjects = 0;
    $numberOfElectiveSubjects = 0;
    $chosenSubjectPoints = 0;

    foreach($input as $key => $value){
        if($key == "valasztott-szak"){
            foreach($value as $subjectNames){
                if($subjectNames == 'ELTE' || $subjectNames == 'IK' || $subjectNames == 'Programtervező informatikus'){
                    $mandatorySubject = "matematika";
                    $electiveSubjects = ["biologia", "fizika", "informatika", "kemia"];
                    break;
                }
                if($subjectNames == "PPKE" || $subjectNames == "BTK" || $subjectNames == "Aglisztika"){
                    $mandatorySubject = "angol";
                    $electiveSubjects = ["francia", "német", "olasz", "orosz", "spanyol", "történelem"];
                    break;
                }
            }
        }
        if($key == "erettsegi-eredmenyek"){
            foreach($value as $results){
                $points = rtrim($results['eredmeny'], '%');

                if($points < 20){
                    return("hiba, nem lehetséges a pontszámítás".$results['nev']."tárgyból elért 20% alatti eredmény miatt");
                }

                if($results['tipus'] == "emelt" && $points > 20){
                    $extraPoints += 50;
                }
                if($mandatorySubject == $results['nev']){
                    $basePoints += intval(str_replace('%', '', $results['eredmeny']));
                }
                if(in_array($results['nev'], $electiveSubjects)){
                    if($results['eredmeny'] > $chosenSubjectPoints){
                        $basePoints += intval(str_replace('%', '', $results['eredmeny']));
                        $basePoints -= $chosenSubjectPoints;
                        $chosenSubjectPoints = intval(str_replace('%', '', $results['eredmeny']));
                    }
                }

                if(in_array($results['nev'], $electiveSubjectsForEveryone)){
                    $summaryOfSubjects .= $results['nev'] . ",";
                    $numberOfMandatorySubjects++;
                }
                if (in_array($results['nev'], $electiveSubjects)) {
                    $chosenSubject = true;
                    $numberOfElectiveSubjects++;
                }
                if($mandatorySubject == "angol"){
                    if($results['nev'] == $mandatorySubject){
                        if($results['tipus'] == "emelt"){
                            $mandatorySubjectElevated = true;
                        }
                    }
                }
            }
            if($numberOfMandatorySubjects != 3){
                return("hiba, nem lehetséges a pontszámítás a kötelező érettségi tárgyak hiánya miatt");
            }
            if($numberOfElectiveSubjects == 0){
                return("hiba, nem lehetséges a pontszámítás a kötelezően választható érettségi tárgyak hiánya miatt");
            }
            if($chosenSubject != true){
                return("hiba, nem lehetséges a pontszámítás a kötelezően választható érettségi tárgyak hiánya miatt!");
            }
            if($mandatorySubject == "angol"){
                if($mandatorySubjectElevated == false){
                    return("hiba, nem lehetséges a pontszámítás a kötelező $mandatorySubject (emelt szinten) hiánya miatt!");
                }
            }
        }
        if($key == "tobbletpontok"){
            $languages = array();
            $sameLanguage = false;
            foreach($value as $extrapoint){
                if(in_array($extrapoint['nyelv'],$languages)){
                    $sameLanguage = true;
                }   
                else{
                    array_push($languages,$extrapoint['nyelv']);
                }
                //ellenorizzuk hogy nem tett e két ugyanolyan nyelvbol nyelvizsgat 
                    switch($extrapoint['tipus']){
                        case "B2":
                            $extraPoints += 28;
                            break;
                        
                        case "C1" :
                            $extraPoints += 40;
                            break;
                    }
                    // itt hozza adjuk a pontokat amiket a nyelvvizsgáért kapot
                if($sameLanguage){
                    $extraPoints += -28;
                    //itt meg ha két ugyanolyan nyelvbol tette akkor le vonom a 28pontot a gyengéb nyelvvizsgáért igy csak 40 pontot ér a két ugyanolyan nyelvvizsga
                }
                if($extraPoints > 100){
                    $extra_points = 100;
                    // szaz pontnal tobb extra pontot nem kaphat egy ember
                }
            }
        }

    }
    $summary = ($basePoints * 2) + $extra_points;
    return "$summary (" . $basePoints * 2 . " alappont + $extraPoints többletpont)";
}

?>
