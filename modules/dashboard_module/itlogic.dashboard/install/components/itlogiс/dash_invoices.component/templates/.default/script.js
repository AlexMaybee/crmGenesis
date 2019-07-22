
window.onload = function() {

    let object = new Events();

};


class Events {

    constructor(){

        this.tabs = document.getElementsByClassName('tab');
        this.tabsContent = document.getElementsByClassName('tab-content');
        //console.log(this.tabs.length);
       // console.log(this.tabsContent.length);
        if(this.tabs.length > 0)
            this.tabSelect(this.tabs,this.tabsContent);


    }

    tabSelect(tabs,tabsContent){
        let i = 0,
            child
            self = this;
        for(i; i < tabs.length; i++) {
            tabs[i].onclick = function () {

                //удаление для всех табов и вкладок класса активности
                self.removeActiveClassForAllTabs(tabs,tabsContent);

                child = this.querySelector('input');
                child.checked = true;

                //добавление класса активности табу и его вкладке
                self.checkTabSelected(child,this);

             //   console.log('инпут',child.value);
            };
        }
    }

    removeActiveClassForAllTabs(tabs,tabsContent){
        //let tabs = document.getElementsByClassName('tab'),
        let  elem,elemClasses,
            i = 0,
            p = 0;

        for(i; i < tabs.length; i++) {
            elemClasses = tabs[i].classList;
            if(elemClasses.contains('tab'))
                elemClasses.remove('active-tab');
        }

        for(p; p < tabsContent.length; p++) {
            elemClasses = tabsContent[p].classList;
            if(elemClasses.contains('tab-content'))
                elemClasses.remove('active-tab-content');
        }
    }

    checkTabSelected(input,elem){
        let contentTab = document.getElementById('tab_' + input.value),
        clasLit = contentTab.classList;
        if(input.checked == true) {
            elem.classList.add('active-tab');
            clasLit.add('active-tab-content');

            //  console.log('Значение кнопки: ',contentTab,clasLit);
        }
    }


    
}