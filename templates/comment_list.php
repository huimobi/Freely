<?php declare(strict_types=1); 

function drawCommentList($comments)
{?>

<ul class="comments-list">
        <?php if(count($comments) > 0):
          foreach ($comments as $comment): ?>
        <li class="comment">
          <article class="comment-user-info">
            <img src=<?=$comment->userProfilePic?> alt="User">
            <span class="comment-username"><?= htmlspecialchars($comment->user->userName) ?></span>
            <span class="rating">⭐ <?= $comment->rating ?></span>
          </article>
          <p><?= htmlspecialchars($comment->description)?></p>
        </li>

          <?php endforeach;?>
          <?php else: ?>
          <p id='no_comments'>No comments yet.</p>
        <?php endif; ?>
      </ul>

<?php }?>