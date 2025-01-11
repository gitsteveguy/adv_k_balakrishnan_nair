<?php
require_once("./header.php");
require_once("./admin_protect.php");
if (isset($_POST['add_quiz'])) {
    $quiz_id;
    $total_marks = 0;
    $quiz_name = $_POST['quiz_name'];
    $duration_in_minutes = $_POST['duration_in_minutes'];

    $stmt = $con->prepare("Insert INTO quizzes (quiz_name,duration_in_minutes) VALUES (?,?)");
    $stmt->bind_param("si", $quiz_name, $duration_in_minutes);

    if ($stmt->execute()) {
        $quiz_id = $con->insert_id;
        $stmt->close();

        $questions = $_POST['questions'];
        $stmt2 = $con->prepare("INSERT INTO questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param('issssss', $quiz_id, $question, $opt_a, $opt_b, $opt_c, $opt_d, $correct_option);
        foreach ($questions as $question_data) {
            $question = $question_data['question'];
            $opt_a = $question_data['option_a'];
            $opt_b = $question_data['option_b'];
            $opt_c = $question_data['option_c'];
            $opt_d = $question_data['option_d'];
            $correct_option = $question_data['correct_option'];
            $stmt2->execute();
            $total_marks++;
        }
        $mstmt = $con->prepare("UPDATE quizzes SET total_marks = ? WHERE quiz_id = ?");
        $mstmt->bind_param("ii", $total_marks, $quiz_id);
        $mstmt->execute();
        $mstmt->close();
        header("Location: " . $Globals['domain'] . "/quiz/admin_dashboard.php?status=success");
        exit();
    } else {
        $stmt->close();
    }
}
?>

<body>
    <h2>Welcome <?php echo $_SESSION['user']['first_name'] ?></h2>
    <section class="dashboard-form-section grid">
        <div class="form-border-container">
            <div class="form-container">
                <h2>Create Quiz</h2>
                <form class="add-quiz-form" method="post">
                    <div class="quiz-attributes">
                        <input type="text" maxlength="100" name="quiz_name" placeholder="Quiz Name" required>
                        <input type="number" name="duration_in_minutes" min="10" placeholder="Duration In Mins (min : 10)" required>
                    </div>
                    <div class="question-creation-container">
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
                    <input type="submit" value="Create Quiz" name="add_quiz">
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
        <h3>Question ${questionCount+1}</h3>
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
        addQuestion();
    </script>
</body>
<?php
require_once("./footer.php");
?>