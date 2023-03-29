<form method="POST">
    <input type="hidden" value="rank" name="type" />
    <table class="table table-striped table-fixed align-middle mb-5">
        <thead>
            <th scope="col" style="width: 128px;">#</th>
            <th scope="col">Statement</th>
        </thead>
        <tbody>
            <?php $count = 1 + (($current_page - 1) * 9); ?>
            <?php $count_start = $count; ?>
            <?php
            $progress = "";
            if ($current_page != 3) {
                $progress = "append";
            } else if ($current_page == 3) {
                $progress = "final";
            }
            ?>
            <?php foreach ($allQuestions as $question) : ?>
                <input type="hidden" value="<?= $progress ?>" name="progress" />
                <input type="hidden" value="<?= $count_start ?>" name="count_start" />
                <input type="hidden" value="<?= $count ?>" name="count_end" />
                <?php $id = $question['id']; ?>
                <?php $name = "question" . $count; ?>
                <tr>
                    <th scope="row">
                        <input type="number" name="<?php echo $name; ?>" style="width: 48px" value="<?php echo isset($_POST[$name]) ? htmlspecialchars($_POST[$name], ENT_QUOTES) : ''; ?>" min="1" max="9" required>
                    </th>
                    <td><?php echo $count . ". " . $question['question_text']; ?></td>
                </tr>
                <?php $count++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    if ($current_page == 3) {
        echo "<button type=\"submit\" class=\"btn btn-success float-end\" name=\"submit\">Submit</button>";
    } else {
        echo "<button type=\"submit\" class=\"btn btn-secondary float-end\" name=\"submit\">Next</button>";
        // echo "<a href=\"$rootURL/student/examination/index.php?questionnaire=$questionnaire_id&eval=$evaluation_id&page=$next_page\" class=\"btn btn-secondary float-end\">Next</a> ";
    }
    ?>
</form>