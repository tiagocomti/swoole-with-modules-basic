<?php


namespace app\helpers;


use app\models\System;

class Calculate
{
    /**
     * @param System[] $systems
     * @return string percent
     */
    public static function getInfectionLevel($systems){
        $systems_affected = count($systems);
        $systems_total = System::find()->byContractID($systems[0]->contract_id)->all();
        return Formatter::NumberToPercent(self::ruleOfThree(count($systems_total),100,$systems_affected));
    }

    /**
     * @param System[] $systems
     * @return string percent
     */
    public static function getImpactLevel($systems){
        $users_infected_total = 0;
        $users_infected = 0;

        foreach($systems as $system){
            foreach ($system->fetchs as $fetch){
                $users_infected =+ count($fetch->users);
            }
        }

        foreach(System::find()->byContractID($systems[0]->contract_id)->all() as $system){
            foreach ($system->fetchs as $fetch){
                $users_infected_total =+ count($fetch->users);
            }
        }
        return Formatter::NumberToPercent(self::ruleOfThree($users_infected_total,100,$users_infected));
    }

    /**
     * 5 = 100
     * 1 = X
     *
     * então
     *
     * 5X = 100*1
     *
     * logo
     *
     * x = 100/5 = 20
     *
     * @param float $numemro_base
     * @param float $equivalente
     * @param float $x
     * @return float|int
     */
    public static function ruleOfThree(float $numemro_base, float $equivalente, float $x){
            return ($equivalente*$x)/$numemro_base;
    }
}