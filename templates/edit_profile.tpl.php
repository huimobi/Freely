<?php
declare(strict_types=1);

function drawEditProfileForm($user, array $errors): void { ?>
<main class="form-page">
    <h2>Edit Your Profile</h2>

    <?php if ($errors): ?>
    <ul class="form-error-list">
        <?php foreach ($errors as $e): ?>
        <li class="form-error"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <form action="/actions/action_edit_profile.php" method="post" class="edit-profile__form">
        <input type="hidden" name="user_id" value="<?= $user->id ?>">

    <label> First Name <input type="text" name="first_name" value="<?= htmlspecialchars($user->firstName) ?> "maxlength="30" required> </label>
    <label> Last Name <input type="text" name="last_name" value="<?= htmlspecialchars($user->lastName) ?> "maxlength="30" required> </label>
    <label> Email <input type="email" name="email" value="<?= htmlspecialchars($user->email) ?> "maxlength="30" required> </label>
    <label> Headline <textarea name="headline" maxlength="200" required><?= htmlspecialchars($user->headline  ?? '') ?></textarea> </label>
    <label> Description <textarea name="description" maxlength="1000" required><?= htmlspecialchars($user->description  ?? '') ?></textarea> </label>
    <button type="submit" class="btn btn--primary">Save Changes</button>
  </form>

  
  <form action="/actions/action_delete_account.php" method="post"
        onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');"
        class="delete-account__form">
    <input type="hidden" name="user_id" value="<?= $user->id ?>">
    <button type="submit" class="btn btn--danger">Delete Account</button>
  </form>


</main>
<?php
}
?>