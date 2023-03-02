<form method="POST">
    <input type="hidden" value="choices" name="type" />
    <?php $count = 1; ?>
    <?php while ($row = $questions_res->fetch_assoc()) : ?>
        <div class="mb-4">
            <p><?php echo $count; ?>. <?php echo $row['question_text']; ?></p>
            <?php if (isset($row['question_image'])) : ?>
                <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($row['question_image']); ?>" class="w-100" />
            <?php endif; ?>
        </div>
        <input type="hidden" value="<?php echo $row['id']; ?>" name="question<?php echo $count; ?>_id" />
        <div class="mb-4">
            <label class="form-label" for="question<?php echo $count; ?>">Your Answer</label>
            <select class="form-control" name="question<?php echo $count; ?>_answer" required>
                <option label="Choose your Answer"></option>
                <?php for ($i = 0; $i < 8; $i++) : ?>
                    <option value="<?php echo $letters[$i]; ?>"><?php echo $letters[$i]; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <?php $count++; ?>
    <?php endwhile; ?>
    <button type="submit" class="btn btn-success float-end" name="submit">Submit</button>
</form>