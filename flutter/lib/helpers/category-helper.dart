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

  static String getCategoryIdByMatrix(List<int> selectedCategory) {
    if (selectedCategory.length > 1) {
      int i = selectedCategory[0];
      int j = selectedCategory[1];
      switch (i) {
        case 0:
          switch (j) {
            case 0:
              // Electronics
              return '42';
            case 1:
              // Fashion
              return '93';
          } // end switch j
          break;
        case 1:
          switch (j) {
            case 0:
              // Home & Lawn
              return '100';
            case 1:
              // Outdoors
              return '101';
          } // end switch j
          break;
        case 2:
          switch (j) {
            case 0:
              // Sporting Goods
              return '102';
            case 1:
              // Music
              return '103';
          } // end switch j
          break;
        case 3:
          switch (j) {
            case 0:
              // Collectibles
              return '104';
            case 1:
              // Handmade
              return '106';
          } // end switch j
          break;
      } // end switch i
    } // end if we have a selected category
    return '42';
  } // end function getCategoryIdByName
}
