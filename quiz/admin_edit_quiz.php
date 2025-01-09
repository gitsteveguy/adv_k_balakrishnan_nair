<?php
require_once("./header.php");
require_once("./admin_protect.php");

$quiz_id;
$quiz_attributes;
if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
}
if (isset($_POST['update_quiz'])) {

    // Sanitize input
    $quiz_name = htmlspecialchars($_POST['quiz_name'], ENT_QUOTES, 'UTF-8');
    $duration_in_minutes = (int)$_POST['duration_in_minutes'];

    // Begin transaction
    $con->begin_transaction();

    try {
        // Update quiz
        $stmt = $con->prepare("UPDATE quizzes SET quiz_name = ?, duration_in_minutes = ? WHERE quiz_id = ?");
        if (!$stmt) {
            throw new Exception('Quiz prepare failed: ' . $con->error);
        }
        $stmt->bind_param("sii", $quiz_name, $duration_in_minutes, $quiz_id);

        if (!$stmt->execute()) {
            throw new Exception('Quiz update failed: ' . $stmt->error);
        }
        $stmt->close();

        // Insert new questions
        if (isset($_POST['questions'])) {
            $questions = $_POST['questions'];
            $stmt2 = $con->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt2) {
                throw new Exception('Questions insert prepare failed: ' . $con->error);
            }
            $stmt2->bind_param('issssss', $quiz_id, $question, $opt_a, $opt_b, $opt_c, $opt_d, $correct_option);

            foreach ($questions as $question_data) {
                $question = $question_data['question'];
                $opt_a = $question_data['option_a'];
                $opt_b = $question_data['option_b'];
                $opt_c = $question_data['option_c'];
                $opt_d = $question_data['option_d'];
                $correct_option = $question_data['correct_option'];

                if (!$stmt2->execute()) {
                    throw new Exception('Question insertion failed: ' . $stmt2->error);
                }
            }
            $stmt2->close();
        }

        // Update existing questions
        if (isset($_POST['update_questions'])) {
            $update_questions = $_POST['update_questions'];
            $stmt3 = $con->prepare("UPDATE questions SET question = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_option = ? WHERE question_id = ?");
            if (!$stmt3) {
                throw new Exception('Questions update prepare failed: ' . $con->error);
            }
            $stmt3->bind_param("ssssssi", $question, $opt_a, $opt_b, $opt_c, $opt_d, $correct_option, $question_id);

            foreach ($update_questions as $question_data) {
                $question = $question_data['question'];
                $opt_a = $question_data['option_a'];
                $opt_b = $question_data['option_b'];
                $opt_c = $question_data['option_c'];
                $opt_d = $question_data['option_d'];
                $correct_option = $question_data['correct_option'];
                $question_id = $question_data['question_id'];

                if (!$stmt3->execute()) {
                    throw new Exception('Question update failed: ' . $stmt3->error);
                }
            }
            $stmt3->close();
        }

        // Commit transaction
        $con->commit();
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php?status=success");
    } catch (Exception $e) {
        $con->rollback();
        echo 'Error: ' . $e->getMessage();
    }
}


if (isset($_GET['qid'])) {
    $quiz_id = $_GET['qid'];
    $sql = "SELECT * FROM quizzes WHERE quiz_id=? LIMIT 1";
    $fth_stmt = $con->prepare($sql);
    $fth_stmt->bind_param('i', $quiz_id);
    $fth_stmt->execute();
    $result = $fth_stmt->get_result();
    if ($result->num_rows < 1) {
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
    }
    $quiz_attributes = $result->fetch_assoc();
    $fth_stmt->close();
    $sql = "SELECT * FROM questions WHERE quiz_id=?";
    $question_fetch_stmt = $con->prepare($sql);
    $question_fetch_stmt->bind_param('i', $quiz_id);
    $question_fetch_stmt->execute();
    $questions = $question_fetch_stmt->get_result();
    $question_fetch_stmt->close();
} else {
    header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php");
}


?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <div class="form-border-container">
            <div class="form-container">
                <h2>Update Quiz</h2>
                <form class="add-quiz-form" method="post">
                    <div class="quiz-attributes">
                        <input type="text" maxlength="100" name="quiz_name" placeholder="Quiz Name" required value="<?php echo $quiz_attributes['quiz_name'] ?>">
                        <input type="number" name="duration_in_minutes" placeholder="Duration In Mins" required value="<?php echo $quiz_attributes['duration_in_minutes'] ?>">
                    </div>
                    <div class="question-updation-container border">
                        <h2>Update Questions</h2>
                        <?php
                        $index = 1;
                        while ($question = $questions->fetch_assoc()) {
                        ?>
                            <div class="question-updation-block">
                                <h3>Question <?php echo $index ?></h3>
                                <input type="hidden" name="update_questions[<?php echo $index - 1 ?>][question_id]" value="<?php echo $question['question_id'] ?>">
                                <textarea maxlength="1000" name="update_questions[<?php echo $index - 1 ?>][question]" class="question-text" placeholder="Question" required><?php echo $question['question'] ?></textarea>
                                <div class="question-creation-options">
                                    <input type="text" maxlength="50" name="update_questions[<?php echo $index - 1 ?>][option_a]" placeholder="Option A" required value="<?php echo $question['option_a'] ?>">
                                    <input type="text" maxlength="50" name="update_questions[<?php echo $index - 1 ?>][option_b]" placeholder="Option B" required value="<?php echo $question['option_b'] ?>">
                                    <input type="text" maxlength="50" name="update_questions[<?php echo $index - 1 ?>][option_c]" placeholder="Option C" required value="<?php echo $question['option_c'] ?>">
                                    <input type="text" maxlength="50" name="update_questions[<?php echo $index - 1 ?>][option_d]" placeholder="Option D" required value="<?php echo $question['option_d'] ?>">
                                </div>
                                <div class="correct-option-container">
                                    <label for="update_questions[<?php echo $index - 1 ?>][correct_option]">Select Correct Option : </label>
                                    <select name="update_questions[<?php echo $index - 1 ?>][correct_option]">
                                        <option <?php echo $question['correct_option'] == 'a' ? 'selected' : '' ?> value="a">A</option>
                                        <option <?php echo $question['correct_option'] == 'b' ? 'selected' : '' ?> value="b">B</option>
                                        <option <?php echo $question['correct_option'] == 'c' ? 'selected' : '' ?> value="c">C</option>
                                        <option <?php echo $question['correct_option'] == 'd' ? 'selected' : '' ?> value="d">D</option>
                                    </select>
                                </div>
                                <a href="./admin_quiz_delete_question.php?dqnid=<?php echo $question['question_id'] ?>&qid=<?php echo $quiz_id ?>" class="remove-question-btn"><span class="material-symbols-rounded">
                                        delete
                                    </span> Delete Question</a>
                            </div>
                        <?php
                            $index++;
                        }
                        ?>
                    </div>
                    <div class="question-creation-container border">
                        <h2>Add New Questions</h2>
                        <!-- <div class="question-creation-block">
                            <h3>Question 1</h3>
                            <input type="text" maxlength="1000" name="question" placeholder="Question" required>
                            <div class="question-creation-options">
                                <input type="text" maxlength="50" name="option_a" placeholder="Option A" required>
                                <input type="text" maxlength="50" name="option_b" placeholder="Option B" required>
                                <input type="text" maxlength="50" name="option_c" placeholder="Option C" required>
                                <input type="text" maxlength="50" name="option_d" placeholder="Option D" required>
                            </div>
                            <div class="correct-option-container">
                                <label for="correct_option">Select Correct Option : </label>
                                <select name="correct_option">
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            </div>
                            <button type="button" class="remove-question-btn" onclick="removeQuestion(0)">Remove Question</button>
                        </div> -->
                        <div class="question-btn-container">
                            <button type="button" class="add-question-btn" onclick="addQuestion(0)">Add Question</button>
                        </div>
                    </div>

                    <input type="submit" value="Update Quiz" name="update_quiz">
                </form>
            </div>
        </div>
        </div>
    </section>
    <script>
        let questionCount = 1;

        function addQuestion() {
            const container = document.querySelector(".question-creation-container");

            const newQuestionBlock = document.createElement("div");
            newQuestionBlock.classList.add("question-creation-block");
            questionCount = container.querySelectorAll('.question-creation-block').length
            newQuestionBlock.id = `question-block-${questionCount}`;

            newQuestionBlock.innerHTML = `
        <h3>New Question ${questionCount+1}</h3>
        <textarea maxlength="1000" class="question-text" name="questions[${questionCount}][question]" placeholder="Question" required></textarea>
        <div class="question-creation-options">
            <input type="text" maxlength="50" name="questions[${questionCount}][option_a]" class="question-opt" placeholder="Option A" required>
            <input type="text" maxlength="50" name="questions[${questionCount}][option_b]" class="question-opt" placeholder="Option B" required>
            <input type="text" maxlength="50" name="questions[${questionCount}][option_c]" class="question-opt" placeholder="Option C" required>
            <input type="text" maxlength="50" name="questions[${questionCount}][option_d]" class="question-opt" placeholder="Option D" required>
        </div>
        <div class="correct-option-container">
            <label for="correct_option">Select Correct Option:</label>
            <select name="questions[${questionCount}][correct_option]" required>
                <option value="a">A</option>
                <option value="b">B</option>
                <option value="c">C</option>
                <option value="d">D</option>
            </select>
        </div>
        <button type="button" class="remove-question-btn" onclick="removeQuestion(${questionCount})">Remove Question</button>
    `;

            container.insertBefore(newQuestionBlock, document.querySelector(".question-btn-container"));
        }

        function removeQuestion(index) {
            const questionBlock = document.getElementById(`question-block-${index}`);
            questionBlock.remove();

            // Re-index the remaining question blocks
            const questionBlocks = document.querySelectorAll('.question-creation-block');
            questionBlocks.forEach((block, idx) => {
                block.id = `question-block-${idx}`;
                block.querySelector('h3').textContent = `Question ${idx + 1}`;
                block.querySelector('textarea[name^="questions"]').setAttribute('name', `questions[${idx}][question]`);
                block.querySelectorAll('.question-opt').forEach((input, inputIdx) => {
                    input.setAttribute('name', `questions[${idx}][${['option_a', 'option_b', 'option_c', 'option_d'][inputIdx]}]`);
                });
                block.querySelector('select[name^="questions"]').setAttribute('name', `questions[${idx}][correct_option]`);
                block.querySelector('.remove-question-btn').setAttribute('onclick', `removeQuestion(${idx})`);
            });

        }
    </script>
</body>