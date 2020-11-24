class CategoryHelper {
  static String getCategoryIdByName(String name) {
    switch (name) {
      case 'Electronics':
        return '42';
      case 'Fashion':
        return '93';
      case 'Home & Lawn':
        return '100';
      case 'Outdoors':
        return '101';
      case 'Sporting Goods':
        return '102';
      case 'Music':
        return '103';
      case 'Collectibles':
        return '104';
      case 'Handmade':
        return '106';
      default:
        return '0';
    } // end switch case
  } // end function getCategoryIdByName
}
