    const notification = document.querySelector('#notification');
    const notificationTitle = document.querySelector('#notification-title');
    const notificationText = document.querySelector('#notification-text');
    const notificationClose = document.querySelector('#notification-close');

    function showNotification(message, isError = false) {
      if (!notification) return;
      notificationTitle.textContent = isError ? 'Error' : 'Success';
      notificationText.textContent = Array.isArray(message) ? message.join(' ') : message;
      notification.classList.toggle('is-error', isError);
      notification.classList.toggle('is-success', !isError);
      const icon = notification.querySelector('.notification-icon');
      if (icon) icon.textContent = isError ? '⚠️' : '✓';
      notification.classList.add('is-visible');
    }

    if (notificationClose) {
      notificationClose.addEventListener('click', () => {
        notification.classList.remove('is-visible');
      });
    }

    if (showRegisterButton && registerSection && loginSection) {
      showRegisterButton.addEventListener('click', () => {
        loginSection.hidden = true;
        registerSection.hidden = false;
        registerSection.querySelector('input').focus();
      });
    }

    if (showLoginButton && registerSection && loginSection) {
      showLoginButton.addEventListener('click', () => {
        registerSection.hidden = true;
        loginSection.hidden = false;
        loginSection.querySelector('input').focus();
      });
    }

    // Auto switch to signup tab if URL has ?tab=signup
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'signup' && registerSection && loginSection) {
      loginSection.hidden = true;
      registerSection.hidden = false;
    }
