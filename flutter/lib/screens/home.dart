import 'package:flutter/material.dart';
import 'package:resold/screens/tabs/browse.dart';
import 'package:resold/screens/tabs/sell.dart';
import 'package:resold/screens/tabs/account.dart';
import 'package:resold/screens/tabs/orders.dart';
import 'package:resold/screens/tabs/search.dart';
import 'package:resold/screens/messages/inbox.dart';
import 'package:resold/view-models/response/magento/customer-response.dart';

class Home extends StatelessWidget {
  final CustomerResponse customer;

  Home(CustomerResponse customer) : customer = customer;

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        title: 'Resold',
        theme: ThemeData(
          primarySwatch: const MaterialColor(0xff41b8ea, {
            50: Color.fromRGBO(25, 72, 92, .1),
            100: Color.fromRGBO(25, 72, 92, .2),
            200: Color.fromRGBO(25, 72, 92, .3),
            300: Color.fromRGBO(25, 72, 92, .4),
            400: Color.fromRGBO(25, 72, 92, .5),
            500: Color.fromRGBO(25, 72, 92, .6),
            600: Color.fromRGBO(25, 72, 92, .7),
            700: Color.fromRGBO(25, 72, 92, .8),
            800: Color.fromRGBO(25, 72, 92, .9),
            900: Color.fromRGBO(25, 72, 92, 1)
          }),
          brightness: Brightness.light,
          accentColor: Colors.white,
          primaryColor: const Color(0xff41b8ea),
          splashColor: const Color(0xff41b8ea),
        ),
        home: HomePage(customer));
  }
}

class HomePageState extends State<HomePage> {
  int selectedTab = 0;
  final CustomerResponse customer;

  HomePageState(this.customer);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align(
                alignment: Alignment.centerLeft,
                child: Image.asset('assets/images/resold-white-logo.png',
                    width: 145, height: 145)),
            Align(
                alignment: Alignment.centerRight,
                child: InkWell(
                  child: Icon(Icons.message, color: Colors.white),
                  onTap: () {
                    Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (context) => InboxPage(customer)));
                  },
                ))
          ],
        ),
        iconTheme: IconThemeData(
          color: Colors.white, //change your color here
        ),
        backgroundColor: const Color(0xff41b8ea),
      ),
      body: Center(
        child: getContent(context),
      ),
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.home), label: 'Buy'),
          BottomNavigationBarItem(icon: Icon(Icons.search), label: 'Search'),
          BottomNavigationBarItem(
              icon: Icon(Icons.attach_money), label: 'Sell'),
          BottomNavigationBarItem(icon: Icon(Icons.receipt), label: 'Orders'),
          BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Account')
        ],
        currentIndex: selectedTab,
        fixedColor: const Color(0xff41b8ea),
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Widget getContent(BuildContext context) {
    switch (selectedTab) {
      case 0:
        return BrowsePage(customer);
      case 1:
        return SearchPage(customer);
      case 2:
        return SellPage(customer);
      case 3:
        return OrdersPage(customer);
      case 4:
        return AccountPage(customer);
      default:
        return Text('Unknown tab');
    }
  }

  void onItemTapped(int index) {
    setState(() {
      selectedTab = index;
    });
  }
}

class HomePage extends StatefulWidget {
  final CustomerResponse customer;

  HomePage(this.customer, {Key key}) : super(key: key);

  @override
  HomePageState createState() => HomePageState(this.customer);
}
