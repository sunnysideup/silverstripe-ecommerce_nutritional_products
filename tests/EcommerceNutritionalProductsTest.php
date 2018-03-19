<?php
class EcommerceNutritionalProductsTest extends SapphireTest {

    protected $usesDatabase = false;

    protected $requiredExtensions = array();

    public function TestDevBuild()
    {
        shell_exec('php framework/cli-script dev/build flush=all', $output, $exitStatus);
        $this->assertEquals(0, $exitStatus);
    }

}

