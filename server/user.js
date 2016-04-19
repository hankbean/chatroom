function User() { 
	var id;
	var name;
	var socket;

	this.setId = function(_id) { 
		id = _id; 
	}; 
	this.getId = function() { 
		return id;
	}; 
	this.setName = function(_name) { 
		name = _name; 
	}; 
	this.getName = function() { 
		return name;
	}; 
	this.setSocket = function(_socket) { 
		socket = _socket; 
	}; 
	
	this.getSocket = function() { 
		return socket;
	}; 
}; 
module.exports = User;