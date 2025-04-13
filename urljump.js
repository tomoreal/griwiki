window.addEventListener("DOMContentLoaded", function () {
    // 全てのtextareaに対して処理を適用
    document.querySelectorAll("textarea").forEach(textarea => {
      textarea.addEventListener("dblclick", function (event) {
        const cursorPos = textarea.selectionStart;
        const text = textarea.value;
        const word = getWordAt(text, cursorPos);
  
        // URLとして妥当ならジャンプ
        if (/^https?:\/\/[^\s]+$/.test(word)) {
          window.open(word, "_blank"); // 新しいタブで開く
        }
      });
    });
  
    // 指定位置の単語（URL候補）を抽出
    function getWordAt(str, pos) {
      const left = str.slice(0, pos).search(/\S+$/);
      const right = str.slice(pos).search(/\s/);
      return str.slice(left, right < 0 ? str.length : pos + right);
    }
  });
  