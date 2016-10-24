<?php

class NutriHolder extends DataObject
{

    private static $singular_name = 'Nutritional Information Profile';
    public function i18n_singular_name()
    {
        return self::$singular_name;
    }

    private static $plural_name = 'Nutritional Information Profiles';
    public function i18n_plural_name()
    {
        return self::$plural_name;
    }

    private static $db = array(
        'ProfileName' => 'Varchar(50)',
        'ServingCount' => 'Int',
        'Container' => 'Varchar(10)',
        'ServingSize' => 'Varchar(15)',
        'AdditionalInfo' => 'Varchar(100)'
    );


    /**
     * @inherited
     */
    private static $casting = array(
        'Title' => "Varchar",
        'Per100Unit' => "Varchar"
    );


    /**
     * Deleting Permissions
     * @return boolean
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     * Returns the unit for the 'Per 100' column of nutritional information. It tries
     * to get the unit from the ServingSize field, and if no match returns 'g'
     * @return String
     */
    function Per100Unit(){ return $this->getPer100Unit();}
    function getPer100Unit(){
        $string = trim($this->ServingSize);
        $matches = array();
        $matchResult = preg_match ('/ ?([A-Z]|[a-z]){1,7}/' ,$string, $matches);
        if (!$matchResult) return "g";
        return trim($matches[0]);
    }

    /**
     * @return String
     */
    function Title(){ return $this->getTitle();}
    function getTitle(){
        $string = $this->ProfileName;
        if(!$string) {
            $string = 'Profile #'.$this->ID.' (please customise) ';
        }
        $string .= ': ';
        $string .= "serving: ".$this->ServingCount."; ";
        $string .= "size: ".$this->ServingSize."; ";
        $string .= "container: ".$this->Container."; ";
        return $string;
    }

    private static $summary_fields = array(
        'Title' => 'Title',
    );

    private static $searchable_fields = array(
        'ProfileName' => 'PartialMatchFilter',
        'ServingCount' => 'ExactMatchFilter',
        'Container' => 'PartialMatchFilter',
        'ServingSize' => 'ExactMatchFilter',
        'AdditionalInfo' => 'Varchar(100)',
        'AdditionalInfo' => 'PartialMatchFilter'
    );


    public function getCMSFields()
    {

        $fields = parent::getCMSFields();

        $fields->removeFieldFromTab('Root.Main', 'SortOrder');

        return $fields;
    }

    /**
     * @return DataList
     */
    function ShownNutriRows()
    {
        return $this->NutriRows()
            ->exclude(array("Hide" => 1));
    }

}
