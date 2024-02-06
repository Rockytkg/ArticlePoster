// 扩展配置对象
const CONFIG = {
  messages: {
    // 按钮图标，支持html
    downloadIcon: "下载海报",
    errorIcon: "生成失败...",
    waitIcon: "生成中...",

    // 提示信息
    idError: "文章ID获取失败，请联系管理员",
    success: "操作成功！",
    fail: "操作失败，请重试。",
    loading: "加载中，请稍候...",
    warning: "请注意，操作有潜在风险。",

    // 保存的文件名
    downloadFileName: "海报",
  },
  dialog: {
    showAlert: (message, type = "info") => {
      // 替换为自定义弹窗
      switch (type) {
        case "success":
          console.log(`成功: ${message}`);
          break;
        case "error":
          console.error(`错误: ${message}`);
          break;
        case "warning":
          console.warn(`警告: ${message}`);
          break;
        default:
          console.log(`信息: ${message}`);
      }
    },
    
    updateButtonState: (button, text, disabled) => {
      if (button) {
        button.innerHTML = text || "";
        button.disabled = !!disabled;
      }
    },
    setElementDisplay: (selectors, display) => {
      document
        .querySelectorAll(selectors)
        .forEach((elem) => (elem.style.display = display));
    },
  },
};

const initArticlePoster = () => {
  const createPoster = async () => {
    const articlePoster = document.querySelector(".article-poster");
    const id = articlePoster?.getAttribute("data-id");
    const button = document.querySelector(".article-poster-button");

    if (!id) return CONFIG.dialog.showAlert(CONFIG.messages.idError, "error");

    CONFIG.dialog.updateButtonState(button, CONFIG.messages.waitIcon, true);

    try {
      const response = await fetch(`/index.php/ArticlePoster/make?cid=${id}`);
      const json = await response.json();

      if (json.code === 200) {
        CONFIG.dialog.setElementDisplay(
          ".article-poster, .poster-popover-mask, .poster-popover-box",
          "block"
        );
        document.querySelector(".article-poster-images").src = json.data;
        document.querySelector(".poster-download").dataset.url = json.data;
        CONFIG.dialog.updateButtonState(
          button,
          CONFIG.messages.downloadIcon,
          false
        );
        CONFIG.dialog.showAlert(CONFIG.messages.success, "success");
      } else {
        throw new Error(json.data || CONFIG.messages.fail);
      }
    } catch (error) {
      CONFIG.dialog.showAlert(error.message, "error");
      CONFIG.dialog.updateButtonState(button, CONFIG.messages.errorIcon, false);
    }
  };

  const downloadPoster = () => {
    const url = document.querySelector(".poster-download").dataset.url;
    const a = document.createElement("a");
    a.href = url;
    a.download = CONFIG.messages.downloadFileName || "下载内容";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  };

  const handleDocumentClick = (event) => {
    if (event.target.matches(".article-poster-button")) createPoster();
    else if (event.target.matches('[data-event="poster-close"]'))
      CONFIG.dialog.setElementDisplay(
        ".article-poster, .poster-popover-mask, .poster-popover-box",
        "none"
      );
    else if (event.target.matches('[data-event="poster-download"]'))
      downloadPoster();
  };

  document.removeEventListener("click", handleDocumentClick);
  document.addEventListener("click", handleDocumentClick);
};

initArticlePoster();
