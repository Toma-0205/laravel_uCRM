const nl2br = (str) => {
    var res = str.replace(/\r\n/g, "<br />");
    res = res.replace(/(\n|\r)/g, "<br />");
    return res;
}

const getToday = () => {
    const today = new Date();
    const yyyy = today.getFullYear();      // 年を取得
    const mm = ("0"+(today.getMonth()+1)).slice(-2);    // 月を取得 (0が1月なので +1 が必要)
    const dd = ("0"+today.getDate()).slice(-2)           // 日を取得
    return yyyy+'-'+mm+'-'+dd
}

export { nl2br, getToday}