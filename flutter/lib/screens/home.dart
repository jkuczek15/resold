import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:resold/screens/browse.dart';
import 'package:resold/screens/sell.dart';
import 'package:resold/screens/account.dart';
import 'package:resold/screens/orders.dart';
import 'package:resold/screens/search.dart';
import 'package:resold/view-models/response/customer-response.dart';

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
              50:  Color.fromRGBO(25,72,92, .1),
              100: Color.fromRGBO(25,72,92, .2),
              200: Color.fromRGBO(25,72,92, .3),
              300: Color.fromRGBO(25,72,92, .4),
              400: Color.fromRGBO(25,72,92, .5),
              500: Color.fromRGBO(25,72,92, .6),
              600: Color.fromRGBO(25,72,92, .7),
              700: Color.fromRGBO(25,72,92, .8),
              800: Color.fromRGBO(25,72,92, .9),
              900: Color.fromRGBO(25,72,92, 1)
            }),
            accentColor: Colors.white,
            primaryColor: const Color(0xff41b8ea)
        ),
        home: HomePage(customer)
    );
  }
}

class HomePageState extends State<HomePage> {

  int selectedTab = 0;
  Position currentLocation;
  final CustomerResponse customer;

  HomePageState(this.customer);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row (
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Align (
                alignment: Alignment.centerLeft,
                child: Image.asset('assets/images/resold-white-logo.png', width: 145, height: 145)
            ),
            Align (
                alignment: Alignment.centerRight,
                child: Icon(Icons.message, color: Colors.white),
            )
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
          BottomNavigationBarItem(icon: Icon(Icons.home), title: Text('Buy')),
          BottomNavigationBarItem(icon: Icon(Icons.search), title: Text('Search')),
          BottomNavigationBarItem(icon: Icon(Icons.attach_money), title: Text('Sell')),
          BottomNavigationBarItem(icon: Icon(Icons.receipt), title: Text('Orders')),
          BottomNavigationBarItem(icon: Icon(Icons.person), title: Text('Account')),
        ],
        currentIndex: selectedTab,
        fixedColor: const Color(0xff41b8ea),
        unselectedItemColor: Colors.black,
        onTap: onItemTapped,
      ),
    );
  }

  Widget getContent(BuildContext context) {
    switch(selectedTab) {
      case 0: return BrowsePage();
      case 1: return SearchPage();
      case 2: return SellPage();
      case 3: return OrdersPage();
      case 4: return AccountPage(customer);
      default: return Text('Unknown tab');
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