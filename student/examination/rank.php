<form method="POST">
    <input type="hidden" value="rank" name="type" />
    <?php for ($i = 1; $i <= 3; $i++) : ?>
        <h3>Group <?php echo $i; ?></h3>
        <table class="table table-striped table-fixed align-middle mb-5">
            <thead>
                <th scope="col" style="width: 128px;">#</th>
                <th scope="col">Statement</th>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php foreach ($allQuestions as $question) : ?>
                    <?php $id = $question['id']; ?>
                    <?php $name = "question" . $count; ?>
                    <?php if ($question['group'] == $i) : ?>
                        <tr>
                            <th scope="row">
                                <input type="number" name="<?php echo $name; ?>" style="width: 48px" min="1" max="9" required>
                            </th>
                            <td><?php echo $count . ". " . $question['question_text']; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php $count++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endfor; ?>
    <button type="submit" name="submit" class="btn btn-success float-end">Submit</button>
</form>