<?php


namespace app\helpers;


use app\enums\Recommendations;
use app\models\System;

class Autofill
{
    /**
     * @param $variables
     * @param System[] $systems
     * < 35% low
     * > 35% and < 50% = medium
     * > 50% and < 80% = high
     * > 80% = extreme
     * @return mixed
     */
    public static function targetVariablesRecommendations($variables, $systems){
        if(isset($variables->infection_level)){
            $percent_infection = (int) Calculate::getInfectionLevel($systems);
            if($percent_infection < 35){$variables->infection_level->recomendation = Recommendations::LEVEL_LOW;}
            else if($percent_infection >= 35 && $percent_infection < 50){$variables->infection_level->recomendation = Recommendations::LEVEL_MEDIUM;}
            else if($percent_infection >= 50 && $percent_infection < 80){$variables->infection_level->recomendation = Recommendations::LEVEL_HIGH;}
            else if($percent_infection > 80){$variables->infection_level->recomendation = Recommendations::LEVEL_EXTREME;}
        }

        if(isset($variables->impact_level)){
            $percent_impact = (int) Calculate::getImpactLevel($systems);
            if($percent_impact < 35){$variables->impact_level->recomendation = Recommendations::LEVEL_LOW;}
            else if($percent_impact >= 35 && $percent_impact < 70){$variables->impact_level->recomendation = Recommendations::LEVEL_MEDIUM;}
            else if($percent_impact > 70){$variables->impact_level->recomendation = Recommendations::LEVEL_HIGH;}
        }

        return $variables;
    }
}