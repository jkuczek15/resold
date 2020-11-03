import 'package:flutter/material.dart';
import 'package:material_design_icons_flutter/material_design_icons_flutter.dart';

class DropdownConditionList extends StatefulWidget {

  final DropdownConditionListState state = new DropdownConditionListState();

  DropdownConditionList({Key key}) : super(key: key);

  @override
  DropdownConditionListState createState() => state;
}

class DropdownConditionListState extends State<DropdownConditionList> {

  String conditionSelected;

  final List<String> conditions = [
    'New',
    'Like New',
    'Good',
    'Used',
  ];

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return DropdownButtonFormField<String>(
      value: conditionSelected,
      icon: Icon(Icons.keyboard_arrow_down),
      iconSize: 20,
      elevation: 16,
      style: TextStyle(color: Colors.black),
      focusColor: const Color(0xff41b8ea),
      validator: (value) {
        if (value == null) {
          return 'Please select a condition.';
        }
        return null;
      },
      hint: Text('Condition'),
      onChanged: (String newValue) {
        FocusScope.of(context).requestFocus(FocusNode());
        setState(() {
          conditionSelected = newValue;
        });
      },
      decoration: const InputDecoration(
        border: const OutlineInputBorder(),
      ),
      items: conditions.asMap().entries.map<DropdownMenuItem<String>>((entry) {
        return DropdownMenuItem<String>(
          value: getSelectedConditionId(entry.key).toString(),
          child: Container (
            child: Row (
              mainAxisAlignment: MainAxisAlignment.start,
              children: [
                Icon(getSelectedConditionIcon(entry.key)),
                SizedBox(width: 20),
                Text(entry.value.toString()),
              ],
            )
          )
        );
      }).toList(),
    );
  }

  int getSelectedConditionId(int index) {
    switch(index) {
      case 0:
        return 235;
      case 1:
        return 236;
      case 2:
        return 237;
      case 3:
        return 238;
      default:
        return 235;
    }
  }

  IconData getSelectedConditionIcon(int index) {
    switch (index) {
      case 0:
      // New
        return MdiIcons.emoticonExcitedOutline;
      case 1:
      // Like New
        return MdiIcons.emoticonHappyOutline;
      case 2:
      // Good
        return MdiIcons.emoticonNeutralOutline;
      case 3:
      // Used
        return MdiIcons.emoticonSadOutline;
      default:
      // New
        return MdiIcons.emoticonExcitedOutline;
    }
  }
}
