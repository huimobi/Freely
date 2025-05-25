<?php
declare(strict_types=1);

function drawEditProfileForm(array $userInfo, array $errors): void { ?>
<main class="form-page">
  <h2>Edit Your Profile</h2>

  <?php if ($errors): ?>
    <ul class="form-error-list">
        <?php foreach ($errors as $e): ?>
        <li class="form-error"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form action="/actions/action_edit_profile.php" method="post" class="edit-profile__form" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
    <input type="hidden" name="user_id" value="<?= $userInfo['user']->id ?>">

    <label> First Name<input type="text" name="first_name" value="<?= htmlspecialchars($userInfo['user']->firstName) ?> "maxlength="30" required> </label>
    <label> Last Name<input type="text" name="last_name" value="<?= htmlspecialchars($userInfo['user']->lastName) ?> "maxlength="30" required> </label>
    <label> Username<input type="text" name="username" value="<?= htmlspecialchars($userInfo['user']->userName) ?>" maxlength="30" required> </label>
    <label> Email<input type="email" name="email" value="<?= htmlspecialchars($userInfo['user']->email) ?> "maxlength="30" required> </label>
    <label> New Password<input type="password" name="new_password" placeholder="Leave blank to keep current password"> </label>
    <label> Headline<textarea name="headline" maxlength="200" ><?= htmlspecialchars($userInfo['user']->headline  ?? '') ?></textarea> </label>
    <label> Description<textarea name="description" maxlength="1000" ><?= htmlspecialchars($userInfo['user']->description  ?? '') ?></textarea> </label>

    <img id="profile-pic-preview" src="<?= $userInfo['profilePic'] ?>" alt="Profile picture" width="150" height="150">
    <label>Profile Photo: <input type="file" name="photo" accept="image/jpeg,image/png" id="profile-photo-input"></label>

    <button type="submit" class="btn btn--primary">Save Changes</button>
  </form>

  
  <form action="/actions/action_delete_account.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');" class="delete-account__form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->getCsrfToken(), ENT_QUOTES) ?>">
    <input type="hidden" name="user_id" value="<?= $userInfo['user']->id ?>">
    <button type="submit" class="btn btn--danger">Delete Account</button>
  </form>
</main>
<?php } ?>