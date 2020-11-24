class ConditionHelper {
  static int getConditionIdByName(String name) {
    switch (name) {
      case 'New':
        return 235;
      case 'Like New':
        return 236;
      case 'Good':
        return 237;
      case 'Used':
        return 238;
      default:
        return 0;
    } // end switch case
  } // end function getConditionIdByName
}
