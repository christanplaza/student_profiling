<form method="POST">
    <input type="hidden" value="range" name="type" />
    <?php $count = 1 + (($current_page - 1) * $per_page); ?>
    <?php $count_start = $count; ?>
    <?php while ($row = $questions_res->fetch_assoc()) : ?>
        <?php
        $progress = "";
        if ($current_page != $total_pages) {
            $progress = "append";
        } else if ($current_page == $total_pages) {
            $progress = "final";
        }
        ?>
        <input type="hidden" value="<?= $progress ?>" name="progress" />
        <input type="hidden" value="<?= $count_start ?>" name="count_start" />
        <input type="hidden" value="<?= $count ?>" name="count_end" />
        <div class="mb-4">
            <p><?php echo $count; ?>. <?php echo $row['question_text']; ?></p>
        </div>
        <input type="hidden" value="<?php echo $row['id']; ?>" name="question<?php echo $count; ?>_id" />
        <div class="d-flex">
            <p><strong><?php echo $row['disagree_text']; ?></strong></p>
            <div class="mx-4">
                <?php for ($i = 1; $i <= $range; $i++) : ?>
                    <div class="form-check form-check-inline">
                        <input style="cursor: pointer;" class="form-check-input" type="radio" name="question<?php echo $count; ?>_answer" id="question<?php echo $count; ?>_answer<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                        <label style="cursor: pointer;" class="form-check-label " for="question<?php echo $count; ?>_answer<?php echo $i; ?>"><?php echo $i; ?></label>
                    </div>
                <?php endfor; ?>
            </div>
            <p><strong><?php echo $row['agree_text']; ?></strong></p>
        </div>
        <hr />
        <?php $count++; ?>
    <?php endwhile; ?>
    <?php
    if ($current_page == $total_pages) {
        echo "<button type=\"submit\" class=\"btn btn-success float-end\" name=\"submit\">Submit</button>";
    } else {
        echo "<button type=\"submit\" class=\"btn btn-secondary float-end\" name=\"submit\">Next</button>";
        // echo "<a href=\"$rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$next_page\" class=\"btn btn-secondary float-end\">Next</a> ";
    }
    ?>
</form>