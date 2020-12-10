class SizeHelper {
  static String getSizeIdByMatrix(List<int> selectedSize) {
    if (selectedSize.length > 1) {
      int i = selectedSize[0];
      int j = selectedSize[1];
      switch (i) {
        case 0:
          switch (j) {
            case 0:
              // Car
              return '239';
            case 1:
              // Pickup Truck
              return '240';
          } // end switch j
          break;
        case 1:
          switch (j) {
            case 0:
              // Delivery Van
              return '241';
            case 1:
              // Moving Truck
              return '242';
          } // end switch j
          break;
      } // end switch i
    } // end if we have a selected category
    return '240';
  } // end function getCategoryIdByName
}
