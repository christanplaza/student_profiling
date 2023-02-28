<form method="POST">
    <input type="hidden" value="range" name="type" />
    <?php $count = 1; ?>
    <?php while ($row = $questions_res->fetch_assoc()) : ?>
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
    <button type="submit" class="btn btn-success float-end" name="submit">Submit</button>
</form>