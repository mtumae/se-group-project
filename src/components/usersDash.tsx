import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CircleUser } from 'lucide-react';



export default function UsersDash() {
     const {data:users, isLoading:usersLoading}=useQuery(convexQuery(api.users.getAllUsers, {}));
    return (
        <div className='mt-10'>
            {usersLoading ? (
              <p>Loading...</p>
            ) : (
              <ul className='w-full'>
                {users?
                users.map((user) => (
                  <li 
                  className='flex justify-between w-full items-center'
                  key={user._id}>
    
                    
                    <div className='flex items-center gap-10'>
                         <CircleUser size={30} />
                        <div className=''>
                            <p>{user.fullName} </p>
                        <p>{user.email}</p>
                     
                    </div>
                    </div>
                    <p className='text-gray-700 text-sm'>{user.role}</p>
                    <p>{new Date(user._creationTime).toLocaleDateString()}</p>
                </li>
                )) : null}
              </ul>
            )}
        </div>
      )
}