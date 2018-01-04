<?php
/*
* Vertekkende van een abstract class runner met een functie voor het vrijgeven van een `property` birthYear
* is het doel van deze oefening om data te verdelen tussen `classen`. Daarna zijn er methods opgenomen
* voor het verwerkenen en de class uit te breiden met properties. Voor $runnersData is met json_decode
* de data niet als array gezet. Zo kan eerste de eerste 'naam' onafhankelijk gelezen worden.
*/
$runnersJsonFile = file_get_contents(__DIR__ . "/runnersData.json");
$runnersData = json_decode($runnersJsonFile);
unset($runnersJsonFile);

function getFirstKey($input)
{
    reset($input);
    $startKey = key($input);
    return $startKey;
}

$firstKey = getFirstKey($runnersData);
$runnerProperties = $runnersData->$firstKey;

abstract class runner
{
    protected function __construct($runnersData)
    {
        foreach ($runnersData as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
    public function get_birthYear()
    {
        return $this->birthYear;
    }
}

/*
* Met een implementatie van priceInterface kunnen
*/
interface priceInterface
{
    public function getPrice();
    public function setPrice($price);
    public function alertPrice();
}

/**
* Vanuit runnerContestData.json aan array ophalen om deze als eigenschappen
* te verwerken in class runnerContest.
*/
$runnersJsonFile = file_get_contents(__DIR__ . "/runnersContestData.json");
$contestData = json_decode($runnersJsonFile, true);
unset($runnersJsonFile);

class runnerContest extends runner implements priceInterface
{
//     private $contestData = array();
    private $category;
    private $price = null;

    public function __construct($data = null, $runnersData)
    {
        parent::__construct($runnersData);
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
        /*
        * Uit class runner met functie get_birthYear de `private property` $birthYear opgehaald worden.
        * Met functie getCategory in class runnersComplementedData is juiste category gefilterd
        * om als property van deze class toe te voegen.
        */
        $age = date('Y') - $this->get_birthYear();
        if (runnersComplementedData::getCategory($age)) {
            $this->category = runnersComplementedData::getCategory($age);
        }
    }

    public function getInfo()
    {
        return $this->firstName.' '.$this->lastName.' in categorie '.$this->category;
    }
    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function alertPrice()
    {
        return sprintf('Voor '.$this->getInfo().' is de prijs: '.$this->getPrice().'€');
    }
}

/*
* In class runnersComplementedData is er een functie getCategory
* die op basis van argument $age de category terug geeft op basis van zijn range.
*/
class runnersComplementedData
{
    public static function getCategory($age=0)
    {
        $categoryAge = array(
          'B' => array(6, 8),
          'K' => array(9, 12),
          'P' => array(13, 16),
          'J' => array(17, 20),
          'S' => array(21, 34),
          'M' => array(35, 99)
        );

        foreach ($categoryAge as $key => $value) {
            if (in_array($age, range($value[0], $value[1]))) {
                return $key;
            }
        }
    }
}


$runner1 = new runnerContest($contestData, $runnerProperties[0]);
echo('<hr>');
var_dump($runner1);
echo('<hr>');
if ($runner1 instanceof priceInterface) {
    print 'De prijsbepaling is verwerkt.';
}
echo('<hr>');
$runner1->setPrice(14);
echo($runner1->alertPrice());
echo('<hr>');


class runnerView extends runnerContest
{
    private $price;

    public function __construct($data, $price)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
        $this->price = $price;
    }

    public function outputDiv($id)
    {
        $html = '<p></p><div id="'.$id.'">'.$this->getInfo().'</div>';
        return $html;
    }

    public function outputTable($data)
    {
        $html = '<table>';

        foreach ($data as $key => $value) {
            if (!is_array($value) and $value<>'') {
                $html .= '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
            } else {
                if ($value<>'') {
                    while (list($var, $val) = each($value)) {
                        foreach ($val as $key => $val) {
                            $html .= '<tr><td>'.$key.'</td><td>'.$val.'</td></tr>';
                        }
                    }
                }
            }
        }
        $html .= '</table>';
        return $html;
    }
}

$runnerView1 = new runnerView($runner1, $runner1->getPrice());
echo $runnerView1->outputDiv('runnerDiv');
$arrayData = (array) $runnerView1;
echo $runnerView1->outputTable($arrayData);

?>
