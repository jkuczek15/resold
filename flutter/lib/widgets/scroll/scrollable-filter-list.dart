import 'dart:collection';

import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';
import 'package:resold/constants/ui-constants.dart';
import 'package:resold/services/resold.dart';

class ScrollableFilterList extends StatefulWidget {
  final ScrollableFilterListState state = new ScrollableFilterListState();

  ScrollableFilterList({Key key}) : super(key: key);

  @override
  ScrollableFilterListState createState() => state;
}

class ScrollableFilterListState extends State<ScrollableFilterList> {
  //List categories =
  Map categoriesMap = {
    'Electronics': Icons.computer,
    'Fashion': MdiIcons.tshirtCrew,
    'Home & Lawn': MdiIcons.sofa,
    'Outdoors': Icons.directions_bike,
    'Music': MdiIcons.guitarAcoustic,
    'Collectibles': MdiIcons.cards,
    'Handmade': MdiIcons.handHeart,
    'Cancel': MdiIcons.close,
  };
  Map conditionMap = {
    'New': MdiIcons.emoticonExcitedOutline,
    'Like New': MdiIcons.emoticonHappyOutline,
    'Good': MdiIcons.emoticonNeutralOutline,
    'Used': MdiIcons.emoticonSadOutline,
    'Cancel': MdiIcons.close,
  };
  List basesortlist = ['Newest', '(\$) Low to High', '(\$) High to Low'];
  double _currentSliderValue = 30;
  List<bool> checkedSortList = [true, false, false];

  TextStyle categoryTextStyle = new TextStyle();

  Column dropdownCategory = Column(
    children: [
      Icon(MdiIcons.filterMenu),
      Text('Category'),
    ],
  );
  Column dropdownCondition = Column(
    children: [
      Icon(MdiIcons.sparkles),
      Text('Condition'),
    ],
  );

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
        padding: EdgeInsets.fromLTRB(0, 0, 0, 10),
        child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: <Widget>[
              PopupMenuButton<PopupMenuItem>(
                  child: dropdownCategory,
                  onSelected: selectedCategory,
                  itemBuilder: (BuildContext context) {
                    var categorylist = List<PopupMenuItem>();
                    categoriesMap.forEach((t, i) => categorylist.add(
                          PopupMenuItem(
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Icon(i),
                                SizedBox(width: 10),
                                Text(t),
                              ],
                            ),
                            value: PopupMenuItem(value: t, child: Text(t)),
                          ),
                        ));
                    return categorylist.map((PopupMenuItem choice) {
                      return PopupMenuItem<PopupMenuItem>(
                        value: choice,
                        child: choice,
                      );
                    }).toList();
                  }),
              PopupMenuButton<PopupMenuItem>(
                  child: Column(
                    children: [
                      Icon(MdiIcons.mapMarkerRadius),
                      Text('Distance'),
                    ],
                  ),
                  itemBuilder: (BuildContext context) {
                    return <PopupMenuEntry<PopupMenuItem<Slider>>>[
                      PopupMenuItem(
                        enabled: false,
                        child: StatefulBuilder(builder:
                            (BuildContext context, StateSetter setState) {
                          var miles = _currentSliderValue.toInt();
                          return Column(children: [
                            Text('$miles mi'),
                            Slider(
                              value: _currentSliderValue > 100
                                  ? 100
                                  : _currentSliderValue,
                              min: 5,
                              max: 100,
                              divisions: 19,
                              activeColor: ResoldBlue,
                              inactiveColor: Colors.grey[300],
                              label: _currentSliderValue.round().toString(),
                              onChanged: (double value) {
                                setState(() {
                                  _currentSliderValue = value;
                                });
                              },
                            ),
                          ]);
                        }),
                      )
                    ];
                  }),
              PopupMenuButton<PopupMenuItem>(
                  child: dropdownCondition,
                  onSelected: selectedCondition,
                  itemBuilder: (BuildContext context) {
                    var conditionList = List<PopupMenuItem>();
                    conditionMap.forEach((t, i) => conditionList.add(
                          PopupMenuItem(
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.start,
                              children: [
                                Icon(i),
                                SizedBox(width: 10),
                                Text(t),
                              ],
                            ),
                            value: PopupMenuItem(value: t, child: Text(t)),
                          ),
                        ));
                    return conditionList.map((PopupMenuItem choice) {
                      return PopupMenuItem<PopupMenuItem>(
                        value: choice,
                        child: choice,
                      );
                    }).toList();
                  }),
              PopupMenuButton<PopupMenuItem>(
                  child: Column(
                    children: [
                      Icon(MdiIcons.sort),
                      Text('Sort'),
                    ],
                  ),
                  onSelected: selectedSort,
                  itemBuilder: (BuildContext context) {
                    var sortlist = List.generate(
                      basesortlist.length,
                      (index) => PopupMenuItem<PopupMenuItem>(
                        value: PopupMenuItem(
                            value: index, child: Text(index.toString())),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.start,
                          children: [
                            Text(basesortlist[index]),
                            SizedBox(
                              width: 10,
                            ),
                            Icon(
                              MdiIcons.check,
                              color: checkedSortList[index]
                                  ? ResoldBlue
                                  : Colors.white,
                            ),
                          ],
                        ),
                      ),
                    );
                    return sortlist.map((PopupMenuItem choice) {
                      return PopupMenuItem<PopupMenuItem>(
                        value: choice,
                        child: choice,
                      );
                    }).toList();
                  }),
            ]));
  }

  void selectedCategory(PopupMenuItem choice) async {
    setState(() {
      if (choice.value != 'Cancel') {
        dropdownCategory = Column(
          children: [
            Icon(categoriesMap[choice.value]),
            Text(choice.value),
          ],
        );
      } else {
        dropdownCategory = Column(
          children: [
            Icon(MdiIcons.filterMenu),
            Text('Category'),
          ],
        );
      }
    });
  }

  void selectedCondition(PopupMenuItem choice) async {
    setState(() {
      if (choice.value != 'Cancel') {
        dropdownCondition = Column(
          children: [
            Icon(conditionMap[choice.value]),
            Text(choice.value),
          ],
        );
      } else {
        dropdownCondition = Column(
          children: [
            Icon(MdiIcons.sparkles),
            Text('Condition'),
          ],
        );
      }
    });
  }

  void selectedSort(PopupMenuItem choice) async {
    setState(() {
      checkedSortList = [false, false, false];
      checkedSortList[choice.value] = true;
    });
  }
}
