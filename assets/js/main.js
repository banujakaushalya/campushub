    const notification = document.querySelector('#notification');
    const notificationTitle = document.querySelector('#notification-title');
    const notificationText = document.querySelector('#notification-text');
    const notificationClose = document.querySelector('#notification-close');

    function showNotification(message, isError = false) {
      notificationTitle.textContent = isError ? 'Error' : 'Success';
      notificationText.textContent = Array.isArray(message) ? message.join(' ') : message;
      notification.classList.toggle('is-error', isError);
      notification.classList.toggle('is-success', !isError);
      notification.querySelector('.notification-icon').textContent = isError ? '⚠️' : '✓';
      notification.classList.add('is-visible');
    }

    notificationClose.addEventListener('click', () => {
      notification.classList.remove('is-visible');
    });

    showRegisterButton.addEventListener('click', () => {
      loginSection.hidden = true;
      registerSection.hidden = false;
      registerSection.querySelector('input').focus();
    });

    showLoginButton.addEventListener('click', () => {
      registerSection.hidden = true;
      loginSection.hidden = false;
      loginSection.querySelector('input').focus();
    });