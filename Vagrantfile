# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "gotardo/wheezy-php"
  
  config.vm.network "private_network", ip: "192.168.33.10"
  
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "256"       
  end

  config.vm.provision :shell, path: "bootstrap.sh"
end
