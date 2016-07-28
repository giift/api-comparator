
        <testcase name= "<?=urldecode($this->e($name))?>" time="<?=$this->e($time)?>" delta-time="<?=$this->e($delta_time)?>">
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
