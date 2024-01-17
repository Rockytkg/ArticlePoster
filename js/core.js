const createPoster = async () => {
  const downloadIcon = '<i class="iconfont icontaiji"> </i>';
  const errorIcon = '<i class="iconfont iconanonymous-full"> </i>';
  const waitIcon = '<i class="iconfont icondengdai"> </i>';
  const articlePoster = document.querySelector(".article-poster");
  const id = articlePoster ? articlePoster.getAttribute("data-id") : null;

  if (!id) {
    alert("文章ID获取失败，请联系管理员");
    return false;
  }

  const articlePosterButton = document.querySelector(".article-poster-button");
  articlePosterButton.innerHTML = waitIcon;
  articlePosterButton.setAttribute("disabled", true);

  try {
    const response = await fetch(`/index.php/ArticlePoster/make?cid=${id}`, {
      method: "GET",
      timeout: 60000,
    });
    const json = await response.json();

    if (json.code === 200) {
      document.querySelector(".article-poster-images").setAttribute("src", json.data);
      document.querySelector(".poster-download").setAttribute("data-url", json.data);
      document.querySelectorAll(".article-poster, .poster-popover-mask, .poster-popover-box").forEach(elem => elem.style.display = 'block');
      articlePosterButton.innerHTML = downloadIcon;
      articlePosterButton.removeAttribute("disabled");
    } else {
      throw new Error(json.data ? json.data : "生成失败，请重试");
    }
  } catch (error) {
    alert(error.message);
    articlePosterButton.innerHTML = errorIcon;
    articlePosterButton.removeAttribute("disabled");
  } finally {
    console.log("finally");
  }
};

const downloadPoster = () => {
  const a = document.createElement("a");
  a.href = document.querySelector(".poster-download").getAttribute("data-url");
  a.download = "海报";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
};

document.addEventListener("click", (event) => {
  if (event.target.matches(".article-poster-button")) {
    createPoster();
  } else if (event.target.matches('[data-event="poster-close"]')) {
    document.querySelectorAll(".article-poster, .poster-popover-mask, .poster-popover-box").forEach(elem => elem.style.display = 'none');
  } else if (event.target.matches('[data-event="poster-download"]')) {
    downloadPoster();
  }
});
