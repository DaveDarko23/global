const fetchAsync = async (options) => {
  let { url, method, success, failed, data } = options;
  try {
    console.log("Estoy en el Fetch");
    let res = await fetch(url, {
      method: method,
      body: data,
    });
    const jsonResponse = await (res.ok ? res.json() : Promise.reject(res));
    success(jsonResponse);
  } catch (err) {
    console.error(err);
    failed();
  }
};

export default fetchAsync;
