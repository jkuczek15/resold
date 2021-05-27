class ConditionHelper {
  static String getConditionIdByName(String name) {
    switch (name) {
      case 'New':
        return '235';
      case 'Like New':
        return '236';
      case 'Good':
        return '237';
      case 'Used':
        return '238';
      default:
        return '0';
    } // end switch case
  } // end function getConditionIdByName

  static String getConditionIdByIndex(int index) {
    switch (index) {
      case 0:
        return '235';
      case 1:
        return '236';
      case 2:
        return '237';
      case 3:
        return '238';
      default:
        return '0';
    } // end switch case
  } // end function getConditionIdByName
}
