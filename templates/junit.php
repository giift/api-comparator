<?xml version="1.0" encoding="UTF-8"?>

<testsuites>
    <testsuite name="comparison test" duration= "<?=$this->e($duration)?>" failures= "<?=$this->e($failures)?>" tests= "<?=$this->e($tests)?>" skipped= "<?=$this->e($skips)?>" errors= "<?=$this->e($errors)?>">
        <?php echo $testcases; ?>

    </testsuite>
</testsuites>