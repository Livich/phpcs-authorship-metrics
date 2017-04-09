<?php
/**
 * Created by IntelliJ IDEA.
 * User: serg
 * Date: 13.02.14
 * Time: 22:49
 */
use utils\Euclid;
use utils\Fraction;

define('TEMPLATES_DIR', './gui');

spl_autoload_register(function ($class) {
    include './' . str_replace('\\', '/', $class) . '.php';
});

if (!isset($_POST['act'])) {
    print(file_get_contents(TEMPLATES_DIR . '/main.html'));
    die;
}

try {
    if (isset($_POST['nominator']) && isset($_POST['denominator'])) {
        $f = new Fraction($_POST['nominator'], $_POST['denominator']);
        $f = Euclid::Reduce($f);
        print(json_encode(array('code' => 200, 'nominator' => $f->getNominator(), 'denominator' => $f->getDenominator())));
    } else {
        throw new Exception('Invalid data', -1);
    }
} catch (Exception $e) {
    print(json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage())));
}