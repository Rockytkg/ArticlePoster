class PosterCreator {
  constructor() {
    this.popoverMask = document.querySelector('.poster-popover-mask');
    this.cid = this.popoverMask.dataset.cid;

    this.init();
  }

  init() {
    document.body.addEventListener('click', (event) => {
      if (event.target.matches('.article-poster-button')) {
        this.createPoster();
      } else if (event.target.matches('.poster-download')) {
        this.downloadPoster(event.target.dataset.url);
      } else if (event.target === this.popoverMask) {
        this.hidePopover();
      }
    });
  }

  async createPoster() {
    console.log('正在生成海报，请稍候...');
    try {
      const response = await fetch(`/index.php/ArticlePoster/make?cid=${this.cid}`);
      const json = await response.json();
      if (json.code === 200) {
        this.showPopover(json.data);
        console.log('操作成功！');
      } else {
        throw new Error(json.data || '操作失败，请重试。');
      }
    } catch (error) {
      console.error(`错误: ${error.message}`);
    }
  }

  downloadPoster(url) {
    const link = document.createElement('a');
    link.href = url;
    link.download = '海报';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  showPopover(url) {
    const posterImages = document.querySelector('.article-poster-images');
    const posterDownload = document.querySelector('.poster-download');
    posterImages.src = url;
    posterDownload.dataset.url = url;
    this.popoverMask.style.display = 'flex';
  }

  hidePopover() {
    this.popoverMask.style.display = 'none';
  }
}

const posterCreator = new PosterCreator();
