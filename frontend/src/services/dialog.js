export default {
    install: (app) => {
      app.config.globalProperties.$confirm = (message) => {
        return window.confirm(message);
      };
    }
  };
  