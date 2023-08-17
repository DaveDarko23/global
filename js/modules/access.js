export default function access(userInfo) {
  localStorage.setItem("PK_Usuario", userInfo.PK_Usuario);
  localStorage.setItem("PK_Type", userInfo.PK_Type);
  localStorage.setItem("username", userInfo.username);
  localStorage.setItem("userType", userInfo.userType);

  location.href = "http://192.168.100.6/Global";
}
