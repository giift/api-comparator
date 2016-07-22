
        <testcase name= "<?=urldecode($this->e($name))?>" time="<?=$this->e($time)?>">
            <?php
            if($error)
            {
            ?>

            <error>
<?php echo $error_message; ?>

            </error>

            <?php
            }
            ?>

            <?php
            if($fail)
            {
            ?>

            <failure message="test failed">
<?php echo $differences; ?>

            </failure>
            <?php
            }
            ?>

        </testcase>
